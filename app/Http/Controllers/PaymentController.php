<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Mail\PaymentReceived;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function __construct()
    {
        try {
            Log::info('Stripe secret key being used', ['key' => config('services.stripe.secret')]);
            Stripe::setApiKey(config('services.stripe.secret'));
        } catch (\Exception $e) {
            Log::error('Failed to initialize Stripe: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createPaymentIntent(Request $request, Booking $booking)
    {
        try {
            Log::info('Starting payment intent creation', [
                'booking_id' => $booking->id,
                'service_price' => $booking->service_price,
                'user_id' => auth()->id()
            ]);

            // Validate booking ownership
            if ($booking->user_id !== auth()->id()) {
                Log::warning('Unauthorized payment attempt', ['booking_id' => $booking->id, 'user_id' => auth()->id()]);
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            // Calculate amount based on payment type
            $amountToCharge = $request->payment_type === 'full'
                ? $booking->service_price
                : ($booking->service_price * 0.10); // 10% deposit

            // Convert amount to cents
            $amountInCents = (int) round($amountToCharge * 100);

            // Create PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'gbp',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'booking_id' => $booking->id,
                    'user_id' => auth()->id(),
                    'barber_id' => $booking->barber_id,
                    'payment_type' => $request->payment_type,
                    'service_price' => $booking->service_price
                ]
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'amount' => $amountToCharge,
                'paymentType' => $request->payment_type
            ]);

        } catch (\Exception $e) {
            Log::error('Payment intent creation failed', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id
            ]);
            return response()->json(['error' => 'Failed to create payment intent'], 500);
        }
    }

    public function processPayment(Request $request, Booking $booking)
    {
        try {
            Log::info('Processing payment', [
                'booking_id' => $booking->id,
                'amount' => $request->amount,
                'payment_intent_id' => $request->payment_intent_id,
                'payment_type' => $request->payment_type
            ]);

            if ($booking->user_id !== auth()->id()) {
                abort(403);
            }

            $paymentAmount = floatval($request->amount);

            if ($request->payment_type === 'deposit') {
                $booking->update([
                    'amount_paid' => $paymentAmount,
                    'deposit_paid' => true,
                    'payment_status' => 'partial',
                    'status' => 'confirmed',
                    'stripe_payment_id' => $request->payment_intent_id
                ]);
            } else {
                $booking->update([
                    'amount_paid' => $booking->service_price,
                    'deposit_paid' => true,
                    'payment_status' => 'completed',
                    'status' => 'confirmed',
                    'stripe_payment_id' => $request->payment_intent_id
                ]);
            }

            Log::info('Payment processed successfully', [
                'booking_id' => $booking->id,
                'amount_paid' => $booking->amount_paid,
                'payment_status' => $booking->payment_status,
                'status' => $booking->status
            ]);

            // Send payment confirmation email
            $booking = Booking::with('barber.user', 'user')->find($booking->id);
            if ($booking->user && $booking->user->email) {
                Mail::to($booking->user->email)->send(new PaymentReceived($booking));
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id
            ]);
            return response()->json(['error' => 'Failed to process payment'], 500);
        }
    }
}
