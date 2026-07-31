<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function create(Event $event)
    {
        $categories = Category::all();
        return view('checkout.create', compact('event','categories'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        if ($event->stock <= 0) {
            return back()->with('error', 'Mohon maaf, tiket untuk acara ini sudah habis.');
        }

        $orderId = 'TRX-' . time() . '-' . Str::random(5);
        
        // Admin fee is added if price > 0
        $totalPrice = $event->price > 0 ? $event->price + 5000 : 0;

        $transaction = Transaction::create([
            'event_id' => $event->id,
            'order_id' => $orderId,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'total_price' => $totalPrice,
            'status' => 'Pending',
        ]);

        // UAS Bypass free event Midtrans
        if ($totalPrice == 0) {
            $transaction->update(['status' => 'success']);
            
            if ($event && $event->stock > 0) {
                $event->stock = $event->stock - 1;
                $event->save();
                
                try {
                    \Illuminate\Support\Facades\Mail::to($transaction->customer_email)
                        ->send(new \App\Mail\EventTicketMail($transaction));
                } catch (\Exception $e) {
                    \Log::error('Gagal mengirim email E-Ticket: ' . $e->getMessage());
                }
            }
            return redirect()->route('checkout.success', $transaction->order_id);
        }

        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'email' => $request->customer_email,
                'phone' => $request->customer_phone,
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);
            return redirect()->route('checkout.payment', $transaction->order_id);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran jaringan: ' . $e->getMessage());
        }
    }

    public function payment($order_id)
    {
        $categories = Category::all();
        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();
        
        // if already success or payment 0, go to success directly
        if ($transaction->status == 'success' || $transaction->status == 'settlement') {
            return redirect()->route('checkout.success', $order_id);
        }

        return view('checkout.payment', compact('transaction','categories'));
    }

    public function success($order_id)
    {
        $categories = Category::all();
        $transaction = Transaction::where('order_id', $order_id)->firstOrFail();

        // Optional fallback check
        if ($transaction->total_price > 0 && $transaction->status == 'Pending') {
            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;
            
            try {
                $status = \Midtrans\Transaction::status($order_id);
                if ($status) {
                    $trx_status = is_array($status) ? ($status['transaction_status'] ?? '') : ($status->transaction_status ?? '');
                    if (in_array($trx_status, ['settlement', 'capture'])) {
                        $this->processSuccess($transaction);
                    }
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        return view('checkout.success', compact('transaction', 'categories'));
    }

    private function processSuccess(Transaction $transaction)
    {
        if (strtolower($transaction->status) === 'pending') {
            $transaction->update(['status' => 'success']);
            
            if ($transaction->event && $transaction->event->stock > 0) {
                $transaction->event->stock = $transaction->event->stock - 1;
                $transaction->event->save();
                
                try {
                    \Illuminate\Support\Facades\Mail::to($transaction->customer_email)
                        ->send(new \App\Mail\EventTicketMail($transaction));
                } catch (\Exception $e) {
                    \Log::error('Gagal mengirim email E-Ticket secara manual (Bypass): ' . $e->getMessage());
                }
            }
        }
    }
}
