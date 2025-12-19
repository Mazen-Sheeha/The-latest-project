<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings, WithColumnWidths
{
    public function collection()
    {
        return Order::with(['products', 'orderProducts'])->get()->map(function ($o) {
            return [
                '#' => $o->id,
                'المنتوجات' => $o->products->pluck('code')->implode(', '),
                'رابط الطلب' => $o->url,
                'الاسم' => explode(" ", $o->name)[0],
                'رقم الهاتف' => $o->phone,
                'المدينة' => explode('/', $o->city)[0],
                'تاريخ الطلب' => $o->created_at->format('H:i - Y/m/d'),
                'سعر الطلب' => "AED " . $o->orderProducts->sum(fn($product) => $product->price * $product->quantity),
                'حالة الطلب' => $this->convertStatusToAr($o->order_status),
                'حالة الدفع' => $o->paid ? "تم" : "لم يتم"
            ];
        });
    }

    public function headings(): array
    {
        return [
            '#',
            'المنتوجات',
            "رابط الطلب",
            "الاسم",
            'رقم الهاتف',
            'المدينة',
            'تاريخ الطلب',
            'سعر الطلب',
            "حالة الطلب",
            'حالة الدفع'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 18,
            'C' => 100,
            'D' => 13,
            'E' => 15,
            'F' => 12,
            'G' => 20,
            'H' => 13,
            'I' => 20,
            'J' => 10,
        ];
    }

    private function convertStatusToAr($status)
    {
        switch ($status) {
            case "waiting_for_confirmation":
                $status = "بانتظار التأكيد";
                break;
            case "waiting_for_shipping":
                $status = "بانتظار الشحن";
                break;
            case "received":
                $status = "تم الاستلام";
                break;
            case "sent":
                $status = "تم الإرسال";
                break;
            case "postponed":
                $status = "تم التأجيل";
                break;
            case "no_response":
                $status = "لا يرد";
                break;
            case "exchanged":
                $status = "تم استبداله";
                break;
            case "rejected_with_phone":
                $status = "تم الإلغاء بالهاتف";
                break;
            case "rejected_in_shipping":
                $status = "تم الإلغاء في الشحن";
                break;
            default:
                $status = "______";
                break;
        }
        return $status;
    }
}
