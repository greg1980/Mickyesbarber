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
                'balance_amount' => $booking->balance_amount,
                'service_price' => $booking->service_price,
                'deposit_amount' => $booking->deposit_amount,
                'user_id' => auth()->id()
            ]);

            // Validate booking ownership
            if ($booking->user_id !== auth()->id()) {
                Log::warning('Unauthorized payment attempt', ['booking_id' => $booking->id, 'user_id' => auth()->id()]);
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            // Calculate amount to charge based on payment status
            if ($booking->deposit_amount <= 0) {
                // First payment - either deposit or full amount
                $amountToCharge = $request->payment_type === 'full'
                    ? $booking->service_price
                    : ($booking->service_price * BookingController::DEPOSIT_PERCENTAGE);
            } else {
                // This is a balance payment
                $amountToCharge = $booking->balance_amount;
            }

            // Validate amount
            if (!is_numeric($amountToCharge) || $amountToCharge <= 0) {
                Log::error('Invalid payment amount', [
                    'booking_id' => $booking->id,
                    'amount' => $amountToCharge,
                    'service_price' => $booking->service_price,
                    'deposit_amount' => $booking->deposit_amount
                ]);
                return response()->json(['error' => 'Invalid payment amount'], 400);
            }

            // Convert amount to cents
            $amountInCents = (int) round($amountToCharge * 100);

            Log::info('Creating payment intent', [
                'amount_to_charge' => $amountToCharge,
                'amount_in_cents' => $amountInCents,
                'is_deposit' => $booking->deposit_amount <= 0,
                'payment_type' => $booking->deposit_amount <= 0 ? ($request->payment_type ?? 'deposit') : 'balance'
            ]);

            // Create PaymentIntent
            $paymentIntent = Stripe\PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'gbp',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'booking_id' => $booking->id,
                    'user_id' => auth()->id(),
                    'barber_id' => $booking->barber_id,
                    'payment_type' => $booking->deposit_amount <= 0 ? ($request->payment_type ?? 'deposit') : 'balance',
                    'service_price' => $booking->service_price
                ]
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'amount' => $amountToCharge,
                'paymentType' => $booking->deposit_amount <= 0 ? ($request->payment_type ?? 'deposit') : 'balance'
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

            Log::info('Webhook received', ['type' => $event->type]);

            // Handle the event
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $bookingId = $paymentIntent->metadata->booking_id;
                    $paymentType = $paymentIntent->metadata->payment_type;

                    Log::info('Payment succeeded', [
                        'booking_id' => $bookingId,
                        'amount' => $paymentIntent->amount,
                        'payment_type' => $paymentType
                    ]);

                    // Update booking status
                    $booking = Booking::find($bookingId);
                    if ($booking) {
                        $amount = $paymentIntent->amount / 100; // Convert from cents to pounds

                        if ($paymentType === 'deposit') {
                            $booking->update([
                                'status' => 'confirmed',
                                'payment_status' => 'deposit_paid',
                                'deposit_amount' => $amount,
                                'balance_amount' => $booking->service_price - $amount
                            ]);
                        } else {
                            // Check if this is a balance payment
                            $totalPaid = $booking->deposit_amount + $amount;
                            $isFullyPaid = abs($totalPaid - $booking->service_price) < 0.01; // Account for floating point precision

                            $booking->update([
                                'status' => 'confirmed',
                                'payment_status' => $isFullyPaid ? 'fully_paid' : 'deposit_paid',
                                'deposit_amount' => $booking->deposit_amount,
                                'balance_amount' => $isFullyPaid ? 0 : ($booking->service_price - $totalPaid)
                            ]);
                        }

                        Log::info('Booking updated after payment', [
                            'booking_id' => $booking->id,
                            'deposit_amount' => $booking->deposit_amount,
                            'balance_amount' => $booking->balance_amount,
                            'status' => $booking->status,
                            'payment_status' => $booking->payment_status
                        ]);

                        // Send confirmation email
                        Mail::to($booking->user->email)->send(new BookingConfirmed($booking));
                    }
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $bookingId = $paymentIntent->metadata->booking_id;

                    Log::info('Payment failed', ['booking_id' => $bookingId]);

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
            Log::error('Webhook error: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Webhook error: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Webhook error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Webhook handling failed'], 500);
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

            // Get the payment amount from the request
            $paymentAmount = floatval($request->amount);

            // Update booking with the payment
            if ($request->payment_type === 'deposit') {
                // This is a deposit payment (25% of service price)
                $booking->update([
                    'deposit_amount' => $paymentAmount,
                    'balance_amount' => $booking->service_price - $paymentAmount,
                    'payment_status' => 'deposit_paid',
                    'status' => 'confirmed',
                    'stripe_payment_id' => $request->payment_intent_id
                ]);
            } else {
                // This is a full payment
                $booking->update([
                    'deposit_amount' => $booking->service_price, // Set deposit to full amount
                    'balance_amount' => 0,
                    'payment_status' => 'fully_paid',
                    'status' => 'confirmed',
                    'stripe_payment_id' => $request->payment_intent_id
                ]);
            }

            Log::info('Payment processed successfully', [
                'booking_id' => $booking->id,
                'deposit_amount' => $booking->deposit_amount,
                'balance_amount' => $booking->balance_amount,
                'payment_status' => $booking->payment_status,
                'status' => $booking->status
            ]);

            return redirect()->route('bookings.user')->with('success',
                $booking->payment_status === 'fully_paid'
                    ? 'Full payment processed successfully!'
                    : 'Deposit payment processed successfully!'
            );
        } catch (\Exception $e) {
            Log::error('Payment processing error', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Payment processing failed. Please try again.');
        }
    }
}
