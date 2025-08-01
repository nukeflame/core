@component('mail::message')
    # Appointment Confirmation

    Dear {{ $appointment->name }},

    Your appointment has been successfully scheduled.

    **Details:**
    - **Date:** {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F j, Y') }}
    - **Time:** {{ $appointment->appointment_time }}
    - **Confirmation Code:** {{ $appointment->confirmation_code }}
    @if ($appointment->purpose)
        - **Purpose:** {{ $appointment->purpose }}
    @endif

    Please arrive early before your scheduled time.

    @component('mail::button', ['url' => route('appointments.index') . '?code=' . $appointment->confirmation_code])
        Manage Appointment
    @endcomponent

    If you need to cancel or reschedule your appointment, please use the confirmation code above.

    Thank you,<br>
    {{ config('app.name') }}
@endcomponent
