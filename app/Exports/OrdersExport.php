<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class OrdersExport implements FromCollection
{
    protected $order;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct(Request $request)
    {
        $this->order = Order::with('user')->orderBy('created_at', 'desc');
        $this->applyFilters($request);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        $orders = $this->order->get()->toArray();

        $addressArray[] = ['User', 'Order ID', 'Status', 'Total', 'Created at'];
        foreach ($orders as $key => $value) {

            $addressArray[] = [$value['user']['name'], $value['id'], $value['order_status'], $value['grand_total'], $value['created_at']];
        }
        return new Collection($addressArray);
    }

    public function applyFilters($request)
    {
        !$request->user || $this->order->whereHas('user', function ($query) use ($request) {
            return $query->where('name', 'like', "%{$request->user}%");
        });

        !$request->status || $this->order->where('orders.order_status', 'like', "%{$request->status}%");

    }
}
