<?php

namespace App\Http\Controllers\Admin;

use App\Library\Grid\Grid;
use App\Models\Order;
use App\Notifications\ProductOrder;
use Illuminate\Http\Request;

class OrderController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = Order::with('user')->latest();
        $grid = (new Grid())
            ->setQuery($results)
            ->setColumns([
                [
                    'field' => 'user',
                    'label' => 'User',
                    //'sortable' => true,
                    'filterable' => [
                        'callback' => function ($query, $value) {
                            $query->whereHas('user', function ($query) use ($value) {
                                $query->where('name', 'like', "%{$value}%");
                            });
                        },
                    ],
                    'formatter' => function ($field, Order $Order) {
                        return $Order->user->name;
                    }
                ],
                [
                    'field' => 'order_type',
                    'label' => 'Order Type',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'order_status',
                    'label' => 'Order Status',
                    'sortable' => true,
                    'filterable' => true,
                    'formatter' => function ($field, Order $Order) {
                        return "<span class='{$Order->order_status}'>{$Order->order_status}</span>";
                    }
                ],
                [
                    'field' => 'created_at',
                    'label' => 'Created At',
                    'sortable' => true
                ],
                [
                    'field' => 'updated_at',
                    'label' => 'Updated At',
                    'sortable' => true
                ]
            ])->setButtons([
                [
                    'label' => 'Edit',
                    'icon' => 'remove_red_eye',
                    'url' => function ($item) {
                        return route('admin.order.show', $item->id);
                    }
                ]

            ])->generate();
        return view('admin.order.grid', compact('grid'));
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::with('user', 'address', 'addressArea', 'addressChiefdom', 'addressSection', 'orderProduct')->find($id);

        return view('admin.order.view', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Order $order)
    {

        $order->order_status = $request->input('status');
        $order->save();

        $order->user->notify(new ProductOrder($order));

        return redirect()->route('admin.order.show', $order->id)->with($this->setMessage(' Update successfully', self::MESSAGE_SUCCESS));
    }
}
