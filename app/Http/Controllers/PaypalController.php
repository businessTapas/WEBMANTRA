<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\{Amount, Item, ItemList, Payer, Payment, PaymentExecution, RedirectUrls, Transaction};
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shipping;
use App\Models\ShippingDetail;
use App\User;
use Notification;
use Helper;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class PaypalController extends Controller
{
    private $_api_context;
    public function __construct()
    {
        $paypal = config('paypal');
        $this->_api_context = new ApiContext(
            new OAuthTokenCredential(
                $paypal['client_id'],
                $paypal['secret']
            )
        );

        $this->_api_context->setConfig($paypal['settings']);

    }

    public function payWithPayPal(Request $request)
    {

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
            //return response()->json(['errors' => $validator->errors()], 422);
            return back()->withErrors($validator)->withInput();

        }
        $request->session()->put('checkoutOrderDetails', $request->all());

        //  --  Paypal process start  ============
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

       /*  $item = new Item();
        $item->setName('Test Item')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice(10);

        $itemList = new ItemList();
        $itemList->setItems([$item]); */

        $amount = new Amount();
        $amount->setCurrency('USD')
               ->setTotal($request->total_amount);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
                   // ->setItemList($itemList)
                    ->setDescription('Testing PayPal payment');

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('paypal.status', [], true))
             ->setCancelUrl(route('paypal.status', [], true));

       /*  $redirectUrls->setReturnUrl(route('paypal.status'))
                     ->setCancelUrl(route('paypal.status')); */

        $payment = new Payment();
        $payment->setIntent('sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions([$transaction]);
               /*  $paydd = $payment->create($this->_api_context);
                dd($paydd); */

        try {
            $payment->create($this->_api_context);

            return redirect($payment->getApprovalLink());
        } catch (\Exception $ex) {
            return back()->withErrors(['error' => $ex->getMessage()]);
        }
    }

    public function getPaymentStatus(Request $request)
    {
        if (empty($request->input('PayerID')) || empty($request->input('paymentId'))) {
            return redirect('/')->with('error', 'Payment failed');
        }

        $paymentId = $request->input('paymentId');
        $payment = Payment::get($paymentId, $this->_api_context);

        $execution = new PaymentExecution();
        $execution->setPayerId($request->input('PayerID'));

        try {
            $result = $payment->execute($execution, $this->_api_context);
//dd($result);
            if ($result->getState() == 'approved') {
                //==== Order srore ================
                $data = $request->session()->get('checkoutOrderDetails', []);
                do {
                    $order_number = $this->generateOrderNumber();
                    $exists = Order::where('order_number', $order_number)->exists();
                } while ($exists);
        
                $data['order_number'] = $order_number;
                if (Auth::check()) {
                    $data['user_id'] = auth()->user()->id;
                }
                $data['payment_status'] = "paid";
                $data['tracking_id'] = $result->getId();
                $data['payment_method'] = "paypal";
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
        
                    ShippingDetail::create($data);
                    if (Auth::check()) {
                        Cart::where('user_id', auth()->user()->id)->delete();
                    } else {
                        session()->forget('cart');
                    }
        
                    DB::commit();
                //================================
                session()->forget('checkoutOrderDetails');
                session()->forget('cart');
                return redirect('/')->with('success', 'Payment success');
            }
        } catch (\Exception $ex) {
            DB::rollBack();

            return redirect('/')->with('error', 'Payment failed');
        }

        return redirect('/')->with('error', 'Payment failed');
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

