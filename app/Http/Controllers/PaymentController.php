<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Mail\BookingConfirmed;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Stripe;

class PaymentController extends Controller
{
    public function __construct()
    {
        try {
            Stripe\Stripe::setApiKey(config('services.stripe.secret'));
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
                'amount' => $booking->deposit_amount,
                'user_id' => auth()->id()
            ]);

            // Validate booking ownership
            if ($booking->user_id !== auth()->id()) {
                Log::warning('Unauthorized payment attempt', ['booking_id' => $booking->id, 'user_id' => auth()->id()]);
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            // Validate amount
            if (!isset($booking->deposit_amount) || !is_numeric($booking->deposit_amount) || $booking->deposit_amount <= 0) {
                Log::error('Invalid deposit amount', [
                    'booking_id' => $booking->id,
                    'amount' => $booking->deposit_amount
                ]);
                return response()->json(['error' => 'Invalid deposit amount'], 400);
            }

            // Convert amount to cents
            $amountInCents = (int) round($booking->deposit_amount * 100);

            // Create PaymentIntent
            $paymentIntent = Stripe\PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'gbp',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'booking_id' => $booking->id,
                    'user_id' => auth()->id(),
                    'barber_id' => $booking->barber_id
                ]
            ]);

            Log::info('Payment intent created', ['intent_id' => $paymentIntent->id]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret
            ]);

        } catch (Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('Payment Error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment processing failed'], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $request->getContent(),
                $sig_header,
                $endpoint_secret
            );

            // Handle the event
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $bookingId = $paymentIntent->metadata->booking_id;

                    // Update booking status
                    $booking = Booking::find($bookingId);
                    if ($booking) {
                        $booking->update([
                            'status' => 'confirmed',
                            'payment_status' => 'paid'
                        ]);

                        // Send confirmation email
                        Mail::to($booking->user->email)->send(new BookingConfirmed($booking));
                    }
                    break;
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $bookingId = $paymentIntent->metadata->booking_id;

                    // Update booking status
                    $booking = Booking::find($bookingId);
                    if ($booking) {
                        $booking->update([
                            'status' => 'payment_failed'
                        ]);
                    }
                    break;
            }

            return response()->json(['status' => 'success']);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook handling failed'], 500);
        }
    }
}
