<?php

namespace App\Models;

use Eav\Model;

class Product extends Model
{
    const ENTITY  = 'product';

    const STOCK_AVAILABILITY_OPTIONS = ['In stock', 'Out of stock'];

    protected $fillable = ['name', 'price', 'quantity', 'weight', 'unit', 'stock_availability','background_image','user_id'];

    protected $casts = [
        'weight' => 'double',
        'price' => 'double'
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function categories()
    {
        return $this->belongsToMany(
            ProductCategory::class,
            'product_product_categories',
            'product_id',
            'product_category_id'
        );
        /*return $this->belongsToMany(PlaceCategories::class,
            'place_place_categories',
            'place_id',
            'place_category_id');*/
    }

    /*public function cartItems()
    {

        // $product->cartItems()->delete();


        return $this->hasMany(CartItem::class);
    }*/
    public function cart()
    {
        return $this->belongsToMany(Cart::class, 'cart_items', 'product_id', 'cart_id')->withPivot('quantity');
    }

    public function getMainTableAttribute($loadedAttributes)
    {
        $mainTableAttributeCollection = $loadedAttributes->filter(function ($attribute) {
            return $attribute->isStatic();
        });

        $mainTableAttribute = $mainTableAttributeCollection->code()->toArray();

        $mainTableAttribute = array_merge($mainTableAttribute, $this->getFillable());

        $mainTableAttribute[] = 'entity_id';
        $mainTableAttribute[] = 'attribute_set_id';

        $mainTableAttribute[] = 'created_at';
        $mainTableAttribute[] = 'updated_at';

        return $mainTableAttribute;
    }

    
}
