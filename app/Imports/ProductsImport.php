<?php

namespace App\Imports;

use App\{Product, Category};
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{ToCollection, WithChunkReading, Importable, WithStartRow};
use \DB;


class ProductsImport implements ToCollection, WithChunkReading, WithStartRow
{
    use Importable;

    protected $product;
    protected $categories;
    protected $errors;
    protected $successInsertCnt;

    protected $inStockTrue;

    public function __construct()
    {
        $this->product = [];
        $this->categories = [];
        $this->errors = [];
        $this->successInsertCnt = 0;

        $this->inStockTrue = "есть в наличие";//@todo::move to lang
    }

    /**
     * @param array $row
     * @return mixed
     */
    protected function handleRow($row=[])
    {
        $data['main_cat_title'] = isset($row[0]) ? strval($row[0]) : "";
        $data['middle_cat_title'] = isset($row[1]) ? strval($row[1]) : "";
        $data['lower_cat_title'] = isset($row[2]) ? strval($row[2]) : "";

        $data['made'] = isset($row[3]) ? strval($row[3]) : "";
        $data['title'] = isset($row[4]) ? strval($row[4]) : "";
        $data['article'] = isset($row[5]) ? strval($row[5]) : "";
        $data['description'] = isset($row[6]) ? strval($row[6]) : "";
        $data['price'] = isset($row[7]) ? floatval($row[7]) : 0;
        $data['warranty'] = (int)$row[8];
        $data['in_stock'] = (isset($row[9]) && $row[9] == $this->inStockTrue) ? true : false;
        $data['category_id'] = null;
        return $data;
    }

    /**
     * @param string $title
     * @param null $category_id
     * @return int
     */
    protected function createCategory($title="", $category_id=null) :int
    {
        $category = Category::firstOrCreate(
            [
                'title' => $title,
                'category_id' => $category_id,
            ],
            [
                'title' => $title,
                'category_id' => $category_id,
            ]);
        $category->save();

        return $category->id;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $i=0;
        foreach ($rows as $row) {
            $data = $this->handleRow($row);

            $validator = Validator::make($data, $this->getValidationRules());
            if ($validator->fails()) {
                $this->errors[] = $validator->getMessageBag()->messages();
                continue;
            }

            if (!isset($this->product[$data['article']])) {
                try {
                    DB::beginTransaction();
                    // Create first level of category
                    $main_cat_id = $this->createCategory($data['main_cat_title']);
                    $this->categories[$data['main_cat_title']] = null;

                    // Create second level of category
                    $middle_cat_id = $this->createCategory($data['middle_cat_title'], $main_cat_id);
                    $this->categories[$data['middle_cat_title']] = $main_cat_id;

                    // Create third level of category
                    $lower_cat_id = $this->createCategory($data['lower_cat_title'], $middle_cat_id);
                    $this->categories[$data['lower_cat_title']] = $middle_cat_id;
                    $data['category_id'] = $lower_cat_id;

                    // create product. if exist - get it
                    $product = Product::firstOrCreate(
                        [
                            'category_id' => $data['category_id'],
                            'article' => $data['article'],
                        ],
                        [
                            'title' => $data['title'],
                            'made' => $data['made'],
                            'description' => $data['description'],
                            'price' => $data['price'],
                            'warranty' => $data['warranty'],
                            'in_stock' => $data['in_stock'],
                        ]
                    );
                    $this->product[$product->article] = $data;
                    DB::commit();
                    $i++;
                } catch (\Throwable $e) {
                    DB::rollback();
                    $this->errors[][] = [$e->getMessage()];
                }
            }
        }
        $this->successInsertCnt += $i;
    }

    /**
     * Excel col rules
     * @return array
     */
    protected function getValidationRules() :array
    {
        return [
            'main_cat_title' => 'required|max:255',
            'middle_cat_title' => 'required|max:255',
            'lower_cat_title' => 'required|max:255',
            'made' => 'required|max:255',
            'title' => 'required|max:255',
            'article' => 'required|max:255',
            'description' => 'required',
            'price' => 'required',
            'warranty' => 'required|integer',
            'in_stock' => 'required|bool',
        ];
    }

    /**
     * @return array
     */
    public function getValidationErrors() :array
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function getSuccessInsertCnt() :int
    {
        return $this->successInsertCnt;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Chunk reading
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}
