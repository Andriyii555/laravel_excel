<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ProductsImport;
use Excel;

class ImportExcelController extends Controller
{
    public function index()
    {
        return view('import_excel');
    }

    public function import(Request $request)
    {
        $this->validate($request, [
           'select_file' => 'required|mimes:xls,xlsx|file:max:2000',
        ]);

//        $path = $request->file('select_file')->getRealPath();
        $path = $request->file('select_file')->store('temp');

//        $test  = Excel::import(new ProductsImport, $path);
//        return redirect('/excel');

        $import = new ProductsImport;
        $import->import($path);

        $errors = $import->getValidationErrors();
        $success_cnt = $import->getSuccessInsertCnt();

        \Session::flash('success', "{$success_cnt} product(s) uploaded successfully.");

        return view('import_excel', ['import_errors' => $errors, 'products' => []]);
//        return redirect('/excel');


//
//        Excel::filter('chunk')->load(database_path('seeds/csv/users.csv'))->chunk(250, function($results) {
//            foreach ($results as $row) {
//                dd($row);
//
//            }
//        });

    }
}
