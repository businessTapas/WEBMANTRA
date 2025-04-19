<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shipping;
use App\Models\ShippingDetail;
use App\User;
use Barryvdh\DomPDF\PDF;
use Notification;
use Helper;
use Illuminate\Support\Str;
use App\Notifications\StatusNotification;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::with('shippingAdd')->whereIn('status', ['new', 'process', 'cancel'])->orderBy('id', 'DESC')->paginate(10);
        return view('backend.order.index')->with('orders', $orders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'fname' => 'required',
            'lname' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pin' => 'required',
            'products' => 'required|array',
            'products.*.id' => 'required|integer',
            'products.*.title' => 'required|string',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.sub_total' => 'required|numeric',
            'gst' => 'required',
            'total_amount' => 'required',
            'quantity' => 'required'

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        do {
            $order_number = $this->generateOrderNumber();
            $exists = Order::where('order_number', $order_number)->exists();
        } while ($exists);

        $data['order_number'] = $order_number;
        if (Auth::check()) {
            $data['user_id'] = auth()->user()->id;
        }

        try {
            DB::beginTransaction();
            $order = Order::create($data);
            $data['order_id'] = $order->id;
            foreach ($data['products'] as $product) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'item' => $product['title'],
                    'quantity' => $product['qty'],
                    'price' => $product['price'],
                    'sub_total' => $product['sub_total'],
                    'gst' => $product['gst'],
                    'total' => $product['total']
                ]);
            }

          /*   ShippingDetail::create($data);
            if (Auth::check()) {
                Cart::where('user_id', auth()->user()->id)->delete();
            } else {
                session()->forget('cart');
            } */

            DB::commit();
            return response()->json(['message' => 'Order placed successfully', 'url' => route('home')]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::find($id);
        // return $order;
        return view('backend.order.show')->with('order', $order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = Order::with('details.prodDetail', 'shippingAdd')->find($id);
        return view('backend.order.edit')->with('order', $order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:new,process,dispatch,cancel',
            'tracking_id' => $request->status === 'dispatch' ? 'required|string' : 'nullable' 
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $order->update([
            'status' => $data['status'],
            'notes' => $data['notes'],
            'tracking_id' => $request->tracking_id ?? null
        ]);

        if ($order) {
            return response()->json([
                'success' => true,
                'message' => 'Order successfully updated',
                'url' => route('order.index')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while updating banner'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        if ($order) {
            $status = $order->delete();
            if ($status) {
                request()->session()->flash('success', 'Order Successfully deleted');
            } else {
                request()->session()->flash('error', 'Order can not deleted');
            }
            return redirect()->route('order.index');
        } else {
            request()->session()->flash('error', 'Order can not found');
            return redirect()->back();
        }
    }

    public function orderTrack()
    {
        return view('pages.order-track');
    }

    public function productTrackOrder(Request $request)
    {
        // return $request->all();
        $order = Order::where('user_id', auth()->user()->id)->where('order_number', $request->order_number)->first();
        if ($order) {
            if ($order->status == "new") {
                request()->session()->flash('success', 'Your order has been placed. please wait.');
                return redirect()->route('home');
            } elseif ($order->status == "process") {
                request()->session()->flash('success', 'Your order is under processing please wait.');
                return redirect()->route('home');
            } elseif ($order->status == "delivered") {
                request()->session()->flash('success', 'Your order is successfully delivered.');
                return redirect()->route('home');
            } else {
                request()->session()->flash('error', 'Your order canceled. please try again');
                return redirect()->route('home');
            }
        } else {
            request()->session()->flash('error', 'Invalid order numer please try again');
            return back();
        }
    }

    // PDF generate
    public function pdf(Request $request)
    {
        $order = Order::getAllOrder($request->id);
        // return $order;
        $file_name = $order->order_number . '-' . $order->first_name . '.pdf';
        // return $file_name;
        $pdf = PDF::loadview('backend.order.pdf', compact('order'));
        return $pdf->download($file_name);
    }
    // Income chart
    public function incomeChart(Request $request)
    {
        $year = \Carbon\Carbon::now()->year;
        // dd($year);
        $items = Order::with(['cart_info'])->whereYear('created_at', $year)->where('status', 'delivered')->get()
            ->groupBy(function ($d) {
                return \Carbon\Carbon::parse($d->created_at)->format('m');
            });
        // dd($items);
        $result = [];
        foreach ($items as $month => $item_collections) {
            foreach ($item_collections as $item) {
                $amount = $item->cart_info->sum('amount');
                // dd($amount);
                $m = intval($month);
                // return $m;
                isset($result[$m]) ? $result[$m] += $amount : $result[$m] = $amount;
            }
        }
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1));
            $data[$monthName] = (!empty($result[$i])) ? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        return $data;
    }

    public function generateOrderNumber(): string
    {
        $year = date('y');
        $month = date('M');

        $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8); // 8 random characters
        $randomNumber = mt_rand(100, 999);
        return $year . $month . $randomString . $randomNumber;
    }
}
