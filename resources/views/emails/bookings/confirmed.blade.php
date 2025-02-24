@component('mail::message')
# Booking Confirmed

Dear {{ $booking->user->name }},

Your booking has been confirmed for:

**Date:** {{ $booking->booking_date->format('l, F j, Y') }}
**Time:** {{ $booking->booking_time }}

Please remember:
- Your deposit of £{{ number_format($booking->deposit_amount, 2) }} has been paid
- The remaining balance of £{{ number_format($booking->service_price - $booking->deposit_amount, 2) }} is to be paid at the salon
- If you need to reschedule, please do so at least 24 hours before your appointment

@component('mail::button', ['url' => route('dashboard')])
View Booking
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
