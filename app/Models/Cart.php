<?php

namespace App\Models;

use App\Logic\SystemConfig;
use Illuminate\Database\Eloquent\Model;
use DB;

class Cart extends Model
{
    protected $fillable = ['digital_address_id'];
    protected $appends = ['digital_administration', 'transport', 'fuel', 'gst', 'gst_rate', 'tip', 'sub_total', 'total'];

    function getDigitalAdministrationAttribute()
    {
        $taxAll = SystemConfig::getOptionGroup(SystemConfig::TAX_GROUP);
        return  $taxAll->{SystemConfig::DIGITAL_ADMINISTRATION};
    }



    function getTransportAttribute()
    {

        $taxAll = SystemConfig::getOptionGroup(SystemConfig::TAX_GROUP);
        return ($taxAll->{SystemConfig::TRANSPORT});
    }

    function getFuelAttribute()
    {

        $taxAll = SystemConfig::getOptionGroup(SystemConfig::TAX_GROUP);
        return ($taxAll->{SystemConfig::FUEL});
    }

    function getGstRateAttribute()
    {

        $taxAll = SystemConfig::getOptionGroup(SystemConfig::TAX_GROUP);
        return ($taxAll->{SystemConfig::GST});
    }


    function getGstAttribute()
    {


        //$subTotal = $this->getSubTotal();
        $subTotal = 0;
        $gstRate = $this->getGstRateAttribute();

        $gst = 0;

        if ($gstRate > 0) {
            $taxAll = SystemConfig::getOptionGroup(SystemConfig::TAX_GROUP);
            $subTotal += $taxAll->{SystemConfig::DIGITAL_ADMINISTRATION};
            $subTotal += $taxAll->{SystemConfig::TRANSPORT};
            $subTotal += $taxAll->{SystemConfig::FUEL};
            $subTotal += $taxAll->{SystemConfig::TIP};

            $gst = $subTotal * $gstRate / 100;
        }

        return ($gst);
    }

    function getTipAttribute()
    {

        $taxAll = SystemConfig::getOptionGroup(SystemConfig::TAX_GROUP);
        return ($taxAll->{SystemConfig::TIP});
    }

    function getSubTotal()
    {

        static $subTotal = null;

        if (is_null($subTotal)) {
            $subTotal = $this->cartItems()->selectRaw('SUM(  p.price * cart_items.quantity ) as subTotal')->join('products as p', 'p.id', 'cart_items.product_id')->first()->subTotal ?? 0;
        }

        return $subTotal;
    }

    function getSubTotalAttribute()
    {
        return ($this->getSubTotal());
    }

    function getTotalAttribute()
    {

        $subtotal = $this->getSubTotal();

        $total = 0;

        if ($subtotal > 0) {
            $taxAll = SystemConfig::getOptionGroup(SystemConfig::TAX_GROUP);

            $total += $taxAll->{SystemConfig::DIGITAL_ADMINISTRATION};
            $total += $taxAll->{SystemConfig::TRANSPORT};
            $total += $taxAll->{SystemConfig::FUEL};

            $total += $taxAll->{SystemConfig::TIP};
            $total += ($total * $taxAll->{SystemConfig::GST} / 100);
            $total += $subtotal;
        }

        return ($total);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function digitalAddresses()
    {
        return $this->belongsTo(DigitalAddress::class, 'digital_address_id');
        //return $this->hasOne(DigitalAddress::class,'digital_address_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }


    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_items', 'cart_id', 'product_id')->using(CartItem::class)->withPivot('quantity')->withTimestamps();
    }
    // public function productCategory()
    // {
    //     return $this->belongsToMany(ProductCategory::class, 'product_product_categories', 'product_id', 'product_category_id')
    //                 ->withPivot('product_id', 'product_category_id')
    //                 ->first(); // Retrieve the first product category if there are multiple
    // }
   
}
