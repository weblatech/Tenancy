<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // یہ لائن لاراویل کو بتائے گی کہ فارم سے کون سا ڈیٹا ڈیٹا بیس میں جانے دینا ہے
    protected $fillable = [
        'name', 'description', 'price', 'compare_price', 'stock', 'image', 'images',
        'variants', 'variant_combinations', 'is_bundle', 'bundle_title', 'bundle_price', 'bundle_details',
        'is_discount', 'discount_badge', 'discount_terms',
        'bundle_header_title', 'bundle_header_badge', 'bundle_options',
        'bundle_color_primary', 'bundle_color_text'
    ];

    protected $casts = [
        'images' => 'array',
        'variants' => 'array',
        'variant_combinations' => 'array',
        'is_bundle' => 'boolean',
        'is_discount' => 'boolean',
        'bundle_options' => 'array',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function getFinalPriceAttribute()
    {
        if ($this->is_bundle && $this->bundle_price !== null) {
            return (float)$this->bundle_price;
        }

        if ($this->is_discount && $this->discount_badge) {
            if (preg_match('/(\d+)\s*%/', $this->discount_badge, $matches)) {
                $percent = (int)$matches[1];
                return $this->price * (1 - $percent / 100);
            }
            if (preg_match('/(\d+)/', $this->discount_badge, $matches)) {
                $percent = (int)$matches[1];
                if ($percent > 0 && $percent <= 100) {
                    return $this->price * (1 - $percent / 100);
                }
            }
        }

        return (float)$this->price;
    }
}