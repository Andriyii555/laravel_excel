<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'title', 'category_id'
    ];

    public $timestamps = false;


    public function categories()
    {
        return $this->hasMany(Category::class);
    }

//    public function subcategory(){
//
//        return $this->hasMany('App\Category', 'parent_id');
//
//    }

    public function childrenCategories()
    {
        return $this->hasMany(Category::class)->with('categories');
    }

}
