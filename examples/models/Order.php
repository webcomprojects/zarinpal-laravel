<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total_amount',
        'notes',
        'payment_status',
        'payment_method',
        'payment_authority',
        'payment_reference_id',
        'payment_card_pan',
        'payment_verified_at',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'subtotal' => 'integer',
        'tax' => 'integer',
        'shipping' => 'integer',
        'discount' => 'integer',
        'total_amount' => 'integer',
        'payment_verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if the order is paid.
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if the order is pending payment.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if the payment failed.
     *
     * @return bool
     */
    public function paymentFailed()
    {
        return $this->payment_status === 'failed';
    }

    /**
     * Calculate the total amount for this order.
     *
     * @return int
     */
    public function calculateTotal()
    {
        return $this->subtotal + $this->tax + $this->shipping - $this->discount;
    }

    /**
     * Update the total amount based on other fields.
     *
     * @return $this
     */
    public function updateTotalAmount()
    {
        $this->total_amount = $this->calculateTotal();
        return $this;
    }
}