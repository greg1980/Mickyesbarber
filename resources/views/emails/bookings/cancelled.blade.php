@component('mail::message')
# Booking Cancelled

Dear {{ $booking->user->name }},

Your booking has been cancelled for:

**Date:** {{ $booking->booking_date->format('l, F j, Y') }}
**Time:** {{ $booking->booking_time }}

@if($booking->booking_date->isAfter(now()->addDay()))
A refund of £{{ number_format($booking->deposit_amount, 2) }} will be processed within 3-5 business days.
@endif

@component('mail::button', ['url' => route('dashboard')])
Book New Appointment
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
