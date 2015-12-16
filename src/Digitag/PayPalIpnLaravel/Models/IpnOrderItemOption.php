<?php

namespace Digitag\PayPalIpnLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class IpnOrderItemOption extends Model
{
    //use SoftDeletingTrait;
    protected $softDelete = true;

    protected $dates = ['deleted_at'];

    protected $table = 'ipn_order_item_options';

    protected $fillable = ['option_name', 'option_selection'];

    public function order()
    {
        return $this->belongsTo('Digitag\PayPalIpnLaravel\Models\IpnOrderItem');
    }
}
