<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'url',
        'shipping_price',
        'city',
        'address',
        'tracking_number',
        'campaign_id',
        "date_of_postponement",
        'notes'
    ];

    public function status()
    {
        $status = $this->order_status;
        $bg = "rgb(157 157 157)";
        $color = "#fff";
        switch ($status) {
            case "waiting_for_confirmation":
                $status = "بانتظار التأكيد";
                break;
            case "waiting_for_shipping":
                $status = "بانتظار الشحن";
                $bg = "rgb(58 146 223)";
                break;
            case "received":
                $status = "تم الاستلام";
                $bg = "#4caf50";
                break;
            case "sent":
                $status = "تم الإرسال";
                $bg = "#7239ea";
                break;
            case "postponed":
                $bg = "#ff701f";
                $status = "تم التأجيل";
                break;
            case "no_response":
                $status = "لا يرد";
                $bg = "#dfcc29";
                $color = '#000';
                break;
            case "exchanged":
                $status = "تم استبداله";
                $bg = "#ffc0cb";
                $color = "#000";
                break;
            case "rejected_with_phone":
                $status = "تم الإلغاء بالهاتف";
                $bg = "#6e1f1f";
                break;
            case "rejected_in_shipping":
                $status = "تم الإلغاء في الشحن";
                $bg = "#a94442";
                break;
            default:
                break;
        }
        $style = '"background-color:' . $bg . '; color:' . $color . ';width: 100% !important;padding: 2px 5px;border-radius: 5px;"';
        echo "<span class='status order_status no-select' style=" . $style . ">" . $status . "</span>";
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['price', 'quantity', 'real_price'])
            ->withTimestamps();
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function blockedNumbers()
    {
        return $this->hasMany(BlockedNumber::class, 'phone', 'phone');
    }

    public function ordersWithSamePhone()
    {
        return $this->hasMany(Order::class, 'phone', 'phone');
    }

    public function phone()
    {
        $phone = $this->phone;
        $phone_count = $this->ordersWithSamePhone->count();
        $href = '';
        if ($phone_count > 1) {
            $bg = "#f55";
            $color = "#fff";
            $href = route('orders.index', ['search' => $phone]);
        } else {
            $bg = "#eee";
            $color = "#000";
        }
        $style = "style='background-color: " . $bg . "; color: " . $color . ";padding: 0 5px; font-size: 10px; border-radius: 100%; '";
        $href = $href ? "href='$href'" : "";
        echo
        "<a class='flex items-center gap-1' " . $href . ">" .
            "<span class='phone'>" . $phone . "</span>" .
            " <div class='flex items-center justify-center'" . $style . ">" .
            $phone_count .
            "</div>" .
            "</a>";
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
