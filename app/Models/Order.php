<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'currency',

        'subtotal',
        'discount_total',
        'shipping_total',
        'tax_total',
        'total',

        'payment_method',
        'payment_method_title',
        'payment_status',
        'transaction_id',
        'ssl_txn_id',
        'ssl_approval_code',

        'customer_note',
        'admin_note',

        'billing_first_name',
        'billing_last_name',
        'billing_company',
        'billing_address_1',
        'billing_address_2',
        'billing_city',
        'billing_state',
        'billing_postcode',
        'billing_country',
        'billing_phone',
        'billing_email',

        'shipping_first_name',
        'shipping_last_name',
        'shipping_company',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_city',
        'shipping_state',
        'shipping_postcode',
        'shipping_country',
        'shipping_phone',

        'preferred_install_date',
        'confirmed_install_date',
        'removal_date',

        'package_id',
        'roofline_footage',
        'service_type',
        'technician_id',

        'is_guest',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'subtotal'               => 'decimal:2',
        'discount_total'         => 'decimal:2',
        'shipping_total'         => 'decimal:2',
        'tax_total'              => 'decimal:2',
        'total'                  => 'decimal:2',
        'preferred_install_date' => 'date',
        'confirmed_install_date' => 'date',
        'removal_date'           => 'date',
        'is_guest'               => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'FLP-' . date('Y') . '-' . str_pad(
                    (Order::whereYear('created_at', date('Y'))->count() + 1),
                    5,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }
}