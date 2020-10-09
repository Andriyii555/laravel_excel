<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'title', 'article', 'description', 'price', 'warranty', 'in_stock', 'category_id', 'made'
    ];


    public function category()
    {
        return $this->hasOne('App\Category', 'id', 'category_id');
//        return $this->belongsTo('App\Category');
    }

}
