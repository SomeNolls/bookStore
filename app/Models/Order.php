<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const DELIVERY_METHODS = [
        'courier' => 'Courier to address',
        'office' => 'Courier office',
        'pickup' => 'Pick up in store',
    ];

    // Orders are paid online by card at checkout. Older orders may still say 'card' or 'cash' (paid on delivery).
    public const PAYMENT_METHODS = [
        'card_online' => 'Card, paid online',
    ];

    /**
     * How the order was paid, for showing to the customer and the admin.
     */
    public function paymentLabel()
    {
        $label = self::PAYMENT_METHODS[$this->payment_method] ?? ucfirst($this->payment_method);

        return $this->card_last4 ? "$label · card ending in {$this->card_last4}" : $label;
    }

    // New orders are 'pending', shown to the customer as Processing. Rentals start once an order is 'completed'.
    public const STATUSES = [
        'pending' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rentals()
    {
        return $this->hasMany(RentedBooks::class);
    }

    /**
     * Moves the order to a new status. Completing it starts its rentals' clocks;
     * cancelling it drops the rentals that never started and puts those copies back up for rent.
     */
    public function changeStatus($status)
    {
        $previous = $this->status;
        $this->update(['status' => $status]);

        if ($status === 'completed' && $previous !== 'completed') {
            $this->rentals()->whereNull('returnDate')->get()
                ->each(fn ($rental) => $rental->update(['returnDate' => now()->addMonths($rental->months ?: 1)]));
        }

        if ($status === 'cancelled') {
            $this->rentals()->whereNull('returnDate')->with('book')->get()->each(function ($rental) {
                optional($rental->book)->increment('forRent');
                $rental->delete();
            });
        }
    }
}
