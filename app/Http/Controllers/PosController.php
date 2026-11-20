<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function index()
    {
        return view('pos', ['menu' => config('menu')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'checkout_token' => 'required|uuid',
            'service' => 'required|in:Dine in,Takeaway',
            'cash' => 'required|integer|min:0|max:100000000',
            'items' => 'required|array|min:1|max:8',
            'items.*.id' => 'required|integer|distinct|in:1,2,3,4,5,6,7,8',
            'items.*.quantity' => 'required|integer|min:1|max:99',
        ]);
        if ($existing = Order::where('checkout_token', $data['checkout_token'])->first()) {
            return response()->json(['receipt' => route('receipts.show', $existing)]);
        }
        $menu = collect(config('menu'))->keyBy('id');
        $items = collect($data['items'])->map(function ($line) use ($menu) {
            $product = $menu[$line['id']];

            return ['id' => $product['id'], 'name' => $product['name'], 'price' => $product['price'], 'quantity' => $line['quantity']];
        });
        $total = $items->sum(fn ($item) => $item['price'] * $item['quantity']);
        if ($data['cash'] < $total) {
            throw ValidationException::withMessages(['cash' => 'Cash received must cover the total.']);
        }
        $order = Order::create([...$data, 'items' => $items->all(), 'total' => $total]);

        return response()->json(['receipt' => route('receipts.show', $order)], 201);
    }

    public function receipt(Order $order)
    {
        return view('receipt', compact('order'));
    }
}
