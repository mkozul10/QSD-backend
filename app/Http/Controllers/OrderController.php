<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeStateRequest;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Auth;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use App\Models\ProductSize;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{

    private function sendEmail($order, $email){
        Mail::send('mail.guestEmail',['data' => $order,'user' => $email], function ($message) use ($email) {
            $message->from('qsdwebshop@gmail.com', 'QSD WebShop')
                    ->to($email) 
                    ->subject('QSD Order details');
        });
    }
    private function checkForSizes($products){
        $result = [];
        $price = 0;
        foreach($products as $product){
            $condition = ProductSize::where('products_id',$product['products_id'])
                                    ->where('sizes_id',$product['sizes_id'])
                                    ->first();
            $condition->load('product');
            if($condition->quantity < $product['quantity']) {
                return response()->json([
                    "message" => "{$condition->product->name} is no longer in stock for the selected size."
                ],200);
            }
            $result[$condition->id] = ['quantity' => $product['quantity']];
            $price += (float) $condition->product->price;
        }
        return ['ids' => $result, 'price' => $price];
    }
    public function payment(OrderRequest $request){
        $guestEmail = $request->guest_email;
        if(!$guestEmail){
            $user = auth('api')->user();
            if($user->status === 0) return response()->json(['message' => 'You are banned'],401);
        }
        $check = $this->checkForSizes($request->products);
        if(!is_array($check)) return $check;
        $amount = $check['price'];
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'usd',
            'payment_method' => $request->payment_method,
            'confirmation_method' => 'manual',
            'confirm' => true,
            'return_url' => 'https://your-website.com/payment/success'
        ]);

        if (!$paymentIntent->status === 'succeeded') {
            return response()->json(['message' => 'Problems with payment'],400);
        }

        if($guestEmail){
            $order = Order::create([
                'address' => $request->address,
                'city' => $request->city,
                'zip_code' => $request->zip_code,
                'phone' => $request->phone,
                'transaction_id' => $paymentIntent->id,
                'price' => $amount,
                'guest_email' => $guestEmail,
            ]);
        } else {
            $order = Order::create([
                'address' => $request->address,
                'city' => $request->city,
                'zip_code' => $request->zip_code,
                'phone' => $request->phone,
                'users_id' => $user->id,
                'transaction_id' => $paymentIntent->id,
                'price' => $amount,
            ]);
        }

        foreach ($check['ids'] as $sizeId => $data) {
            ProductSize::where('id', $sizeId)->decrement('quantity', $data['quantity']);
        }
                    
        $order->products()->sync($check['ids']);
        if($guestEmail) $order->load(['products','products.product']);
        else $order->load(['user','products','products.product']);
        $order->products;
        if($guestEmail) $this->sendEmail($order,$guestEmail);
        else $this->sendEmail($order,$user->email);

        return response()->json([
            "message" => "Order successfully added.",
            "order" => $order,
        ],200);
    }

    public function getOrdersPerUser(){
        $perPage = 10;
        $user = Auth::user();
        $orders = $user->orders()->paginate($perPage);
        $orders->load(['products.product']);
        return response()->json($orders,200);
    }

    public function getOrders(){
        $perPage = 10;
        $orders = Order::with(['user', 'products.product'])->paginate($perPage);
        return response()->json($orders, 200);
    }

    public function updateState(ChangeStateRequest $request){
        $order = Order::find($request->order_id);
        $status = $request->state == 1 ? 'pending' : ($request->state == 2 ? 'shipped' : 
        ($request->state == 3 ? 'delivered' : 'canceled'));
        if($order){
            if($request->comment){
                $order->update([
                    'status' => $status,
                    'comment'=> $request->comment
                ]);
            } else {
                $order->update([
                    'status' => $status
                ]);
            }
            return response()->json(['message' => 'Order state updated successfully'],200);
        }
        return response()->json(['message' => 'Error occured'],500);
    }
}