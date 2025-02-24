<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Mail\BookingConfirmed;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(Booking $booking)
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $booking->deposit_amount * 100, // Convert to cents
                'currency' => 'gbp',
                'metadata' => [
                    'booking_id' => $booking->id
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $bookingId = $paymentIntent->metadata->booking_id;

            $booking = Booking::find($bookingId);
            if ($booking) {
                $booking->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'payment_id' => $paymentIntent->id
                ]);

                // Send confirmation email
                Mail::to($booking->user->email)->send(new BookingConfirmed($booking));
            }
        }

        return response()->json(['status' => 'success']);
    }
}
