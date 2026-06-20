<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['product_id', 'customer_name', 'rating', 'comment', 'show_on_homepage'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}