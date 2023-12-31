<?php

namespace App\Http\Controllers;

use App\Models\CartBody;
use App\Models\CartHead;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class CartHeadController extends Controller
{
    protected function create($account_id)
    {
        $token = Str::random(16) . '_' . time();
        cookie()->queue('cart-token', $token, 2628000);

        $cart = CartHead::create([
            'token' => $token,
            'cart_status' => 0,
            'total_price' => 0,
            'account_id' => $account_id
        ]);

        return $cart;
    }

    protected function cartBody(CartHead $cart, Product $product)
    {
        $body = CartBody::query()->where('cart_id', $cart->id)->where('product_id', $product->id)->first();
        if ($body) {
            if (isset(request()->amount))
                $body->product_count += request()->amount;
            else
                $body->product_count ++;

            $body->product_price = $product->sales_price;
            $body->product_offer = $product->offer_price;
            $body->save();
        } else {
            $body = CartBody::create([
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'product_price' => $product->sales_price,
                'product_offer' => $product->offer_price ?? null,
                'product_count' => request()->amount ?? 1,
                'cart_id' => $cart->id,
            ]);
        }
    }

    public function addToCart(Request $request)
    {
        if (!is_null($request->cookie('cart-token')))
            $cart = CartHead::where('token', $request->cookie('cart-token'))->first();

        if (!isset($cart))
            $cart = $this->create($request->account);

        $product = Product::find($request->product);
        if (!$product) {
            return response()->json(array(
                'message' => 'محصول یافت نشد.'
            ), 404);
        }

        if ($product->account_id != $cart->account_id) {
            return response()->json(array(
                'message' => 'شما در حال خرید از یک فروشگاه دیگر هستید. برای خرید از این فروشگاه لطفا سبد خرید دیگر خود را تکمیل کنید.'
            ), 404);
        }

        $this->cartBody($cart, $product);
        $cartItemCount = fa_number($cart->bodies->count());
        $cart->total_price = $cart->totalPrice();
        $cart->final_price = $cart->finalPrice();
        $cart->save();

        return response()->json(array(
            'cartItemCount' => $cartItemCount,
            'cart' => $cart->id,
        ), 200);
    }

    public function showCart(Request $request)
    {
        $cartItemCount = fa_number(0);
        $cart = null;
        $bodies = null;

        if (!is_null($request->cookie('cart-token'))) {
            $cart = CartHead::where('token', $request->cookie('cart-token'))->first();
            if ($cart) {
                $cartItemCount = fa_number($cart->bodies->count());
                $bodies = $cart->bodies;
            }
        }

        return view('front.shop.cart', compact('cart', 'bodies', 'cartItemCount'));
    }

    public function removeFromCart(CartBody $body)
    {
        $body->delete();
        $cart = $body->head;
        $cart->total_price = $cart->totalPrice();
        $cart->final_price = $cart->finalPrice();
        $cart->save();
        $totalPrice = fa_number($cart->totalPrice());
        $finalPrice = fa_number($cart->finalPrice());
        $cartItemCount = fa_number($cart->bodies->count());

        if ($cart->bodies->count() == 0) {
            $cart->delete();
        }
        return response()->json(array(
            'totalPrice' => $totalPrice,
            'finalPrice' => $finalPrice,
            'cartItemCount' => $cartItemCount,
            'cart' => $cart->id
            // 'showCart' => $cart->showCart()
        ), 200);
    }

    public function amount(Request $request, CartBody $body)
    {
        $body->product_count = $request->amount;
        $body->save();
        $cart = $body->head;
        $cart->total_price = $cart->totalPrice();
        $cart->final_price = $cart->finalPrice();
        $cart->save();
        $bodyPrice = fa_number($body->total());
        $totalPrice = fa_number($cart->totalPrice());
        $finalPrice = fa_number($cart->finalPrice());
        return response()->json(array(
            'bodyPrice' => $bodyPrice,
            'totalPrice' => $totalPrice,
            'finalPrice' => $finalPrice,
            'cart' => $cart->id
            // 'showCart' => $cart->showCart()
        ), 200);
    }

    public function discount(Request $request, CartHead $cart)
    {
        $discount = Discount::query()->where('title', $request->discount)->first();
        if ($discount) {
            if ($discount->isValid()) {
                if ($discount->isValidCart($cart)) {
                    if ($discount->type() == 'price') {
                        $discount_price = $discount->price;
                        $discount_value = $discount->price;
                    } elseif ($discount->type() == 'percent') {
                        $discount_price = $cart->total_price * ($discount->percent / 100);
                        $discount_value = $discount->percent;
                    }
                    $final_price = $cart->total_price - $discount_price;
                    $cart->update([
                        'discount_id' => $discount->id,
                        'discount_title' => $discount->title,
                        'discount_description' => $discount->details,
                        'discount_type' => $discount->type(),
                        'discount_value' => $discount_value,
                        'discount_price' => $discount_price,
                        'final_price' => $final_price
                    ]);
                    return response()->json(array(
                        'finalPrice' => fa_number($final_price),
                        'discountPrice' => fa_number($discount_price),
                        // 'showCart' => $cart->showCart()
                    ), 200);
                } else {
                    $cart->update([
                        'discount_id' => null,
                        'discount_title' => null,
                        'discount_description' => null,
                        'discount_type' => null,
                        'discount_value' => null,
                        'discount_price' => null,
                        'final_price' => null,
                    ]);
                    return response()->json(array(
                        'message' => 'کد تخفیف وارد شده قابل استفاده نمی باشد.',
                    ), 409);
                }
            } else {
                return response()->json(array(
                    'message' => 'کد تخفیف وارد شده اعتبار ندارد.',
                ), 404);
            }
        } else {
            return response()->json(array(
                'message' => 'کد تخفیف یافت نشد.',
            ), 404);
        }
    }

    public function removeDiscount(CartHead $cart)
    {
        $cart->update([
            'discount_id' => null,
            'discount_title' => null,
            'discount_description' => null,
            'discount_type' => null,
            'discount_value' => null,
            'discount_price' => null,
            'final_price' => $cart->total_price,
        ]);
        return response()->json(array(
            'finalPrice' => fa_number($cart->total_price),
            'discountPrice' => fa_number(0),
            'showCart' => $cart->showCart()
        ), 200);
    }
}
