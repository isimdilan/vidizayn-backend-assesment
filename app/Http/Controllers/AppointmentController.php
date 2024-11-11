<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use App\Mail\AppointmentCreated;

class AppointmentController extends Controller
{
    //
    public function store(Request $request)
    {
        $request->validate([
            'provider_id'=>'required|exists:providers,id',
            'date'=>'required|date',
            'time'=>'required|date_format:H:i',
        ]);

        $appointment = Appointment::create([
            'user_id'=>Auth::id(),
            'provider_id'=>$request->provider_id,
            'date'=>$request->date,
            'time'=>$request->time,
        ]);

        Mail::to($appointment->user->email)->queue(new AppointmentCreated($appointment));


        return response()->json(['message' => 'Appointment created successfully', 'appointment' => $appointment]);
    }

    public function index()
    {
        $appointments = Auth::user()->appointments;

        return response()->json(['appointments' => $appointments]);
    }
}
