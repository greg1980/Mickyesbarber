@component('mail::message')
# Appointment Reminder

This is a friendly reminder about your upcoming appointment.

**Date:** {{ $booking->booking_date }}
**Time:** {{ $booking->booking_time }}
**Barber:** {{ $booking->barber->user->name ?? 'N/A' }}

@component('mail::button', ['url' => url('/customer/bookings')])
View My Bookings
@endcomponent

If you have any questions, please contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
