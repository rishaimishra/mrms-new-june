<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OrderReportController extends Controller
{
    protected $order;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Last One Year Digital Address Report";
        $subtitle = "Monthly";

        $this->order = Order::with('user');

        $this->applyFilters($request);

        $orders = $this->order->latest()->paginate();

        if ($request->download) {
            return Excel::download(new OrdersExport($request), 'digital_addresses.xlsx');
        }

        return view('admin.orderreport.list', compact('orders'));
    }

    public function applyFilters($request)
    {
        !$request->user || $this->order->whereHas('user', function ($query) use ($request) {
            return $query->where('name', 'like', "%{$request->user}%");
        });

        !$request->status || $this->order->where('orders.order_status', 'like', "%{$request->status}%");
    }
}
