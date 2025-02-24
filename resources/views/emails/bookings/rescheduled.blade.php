@component('mail::message')
# Booking Rescheduled

Dear {{ $booking->user->name }},

Your booking has been rescheduled:

**From:**
Date: {{ $oldDate->format('l, F j, Y') }}
Time: {{ $oldTime }}

**To:**
Date: {{ $booking->booking_date->format('l, F j, Y') }}
Time: {{ $booking->booking_time }}

Your deposit has been transferred to the new appointment.

@component('mail::button', ['url' => route('dashboard')])
View Booking
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
