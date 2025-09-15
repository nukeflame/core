<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\TimeSlot;
use App\Mail\AppointmentConfirmation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * Get available dates for appointments.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableDates()
    {
        // Get dates for the next 14 days
        $dates = [];
        $startDate = Carbon::now()->addDay(); // Start from tomorrow

        for ($i = 0; $i < 14; $i++) {
            $currentDate = $startDate->copy()->addDays($i);

            // Skip weekends if needed
            if ($currentDate->isWeekend()) {
                continue;
            }

            $dates[] = [
                'value' => $currentDate->format('Y-m-d'),
                'display' => $currentDate->format('F j'), // April 17
            ];
        }

        return response()->json([
            'success' => true,
            'dates' => $dates
        ]);
    }

    /**
     * Get available time slots for a specific date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTimeSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $date = $request->date;

        // Get all available time slots
        $availableSlots = [
            '9:00 AM',
            '10:00 AM',
            '11:00 AM',
            '1:00 PM',
            '2:00 PM',
            '3:00 PM'
        ];

        // Get booked appointments for this date
        $bookedSlots = Appointment::where('appointment_date', $date)
            ->pluck('appointment_time')
            ->toArray();

        // Filter out booked slots
        $availableTimeSlots = array_filter($availableSlots, function ($slot) use ($bookedSlots) {
            return !in_array($slot, $bookedSlots);
        });

        // Format for frontend
        $timeSlots = [];
        foreach ($availableTimeSlots as $slot) {
            $timeSlots[] = [
                'value' => $slot,
                'display' => $slot
            ];
        }

        return response()->json([
            'success' => true,
            'timeSlots' => $timeSlots
        ]);
    }

    /**
     * Store a new appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'selected_date' => 'required|date_format:Y-m-d|after_or_equal:today',
        //     'selected_time' => 'required|string',
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|email|max:255',
        //     'purpose' => 'nullable|string|max:1000',
        // ]);

        return response()->json(['success' => true]);
        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'errors' => $validator->errors()
        //     ], 422);
        // }

        // // Check if the slot is still available
        // $isSlotTaken = Appointment::where('appointment_date', $request->selected_date)
        //     ->where('appointment_time', $request->selected_time)
        //     ->exists();

        // if ($isSlotTaken) {
        //     return response()->json([
        //         'success' => false,
        //         'errors' => ['appointment' => ['This time slot is no longer available. Please select another time.']]
        //     ], 422);
        // }

        // // Create the appointment
        // $appointment = new Appointment();
        // $appointment->appointment_date = $request->selected_date;
        // $appointment->appointment_time = $request->selected_time;
        // $appointment->name = $request->name;
        // $appointment->email = $request->email;
        // $appointment->purpose = $request->purpose;
        // $appointment->confirmation_code = Str::random(10);
        // $appointment->status = 'confirmed';
        // $appointment->save();

        // // Send confirmation email
        // try {
        //     Mail::to($request->email)->send(new AppointmentConfirmation($appointment));
        // } catch (\Exception $e) {
        //     // Log the error but don't fail the request
        //     \Log::error('Failed to send appointment confirmation email: ' . $e->getMessage());
        // }

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Your appointment has been confirmed!',
        //     'appointment' => [
        //         'date' => Carbon::parse($appointment->appointment_date)->format('F j, Y'),
        //         'time' => $appointment->appointment_time,
        //         'confirmation_code' => $appointment->confirmation_code,
        //     ]
        // ]);
    }

    /**
     * Cancel an appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'confirmation_code' => 'required|string',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $appointment = Appointment::where('confirmation_code', $request->confirmation_code)
            ->where('email', $request->email)
            ->first();

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'errors' => ['appointment' => ['No matching appointment found.']]
            ], 404);
        }

        $appointment->status = 'cancelled';
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Your appointment has been cancelled successfully.'
        ]);
    }

    /**
     * Show the admin dashboard for appointments.
     *
     * @return \Illuminate\View\View
     */
    public function adminDashboard()
    {
        $upcomingAppointments = Appointment::where('appointment_date', '>=', Carbon::today())
            ->where('status', 'confirmed')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->paginate(15);

        return view('appointments.admin', compact('upcomingAppointments'));
    }
}
