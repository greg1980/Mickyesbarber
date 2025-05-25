@component('mail::message')
# Payment Confirmation

We have received your payment for your appointment.

**Date:** {{ $booking->booking_date }}
**Time:** {{ $booking->booking_time }}
**Barber:** {{ $booking->barber->user->name ?? 'N/A' }}
**Amount Paid:** £{{ number_format($booking->deposit_amount + $booking->balance_amount, 2) }}

@component('mail::button', ['url' => url('/customer/bookings')])
View My Bookings
@endcomponent

Thank you for your payment!

Thanks,<br>
{{ config('app.name') }}
@endcomponent
