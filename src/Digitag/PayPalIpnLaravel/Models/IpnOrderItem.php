<?php

namespace Digitag\PayPalIpnLaravel\Models;

use Illuminate\Database\Eloquent\Model;

class IpnOrderItem extends Model
{
    protected $softDelete = true;

    protected $dates = ['deleted_at'];

    protected $table = 'ipn_order_items';

    protected $fillable = ['item_name', 'item_number', 'item_number',
        'quantity', 'mc_gross', 'mc_handling', 'mc_shipping', 'tax',
    ];

    public function order()
    {
        return $this->belongsTo('Digitag\PayPalIpnLaravel\Models\IpnOrder');
    }

    public function options()
    {
        return $this->hasMany('Digitag\PayPalIpnLaravel\Models\IpnOrderItemOption');
    }
}
