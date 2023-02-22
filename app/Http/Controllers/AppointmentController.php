<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        return view('appointment.index');
    }

    public function show()
    {
        $user = Auth::user();
        $appointments = Appointment::where('id_user', $user->id)->get();
        return view('appointment.show', ['appointments' => $appointments]);
    }

    public function showId(Appointment $appointmentId)
    {
        return view('appointment.showId', ['appointment' => $appointmentId]);
    }

    public function create()
    {
        $schedules = Schedule::whereNull('appointment_id')->get();
        return view('appointment.create', ['schedules' => $schedules]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required'],
            'date' => ['required'],
        ]);

        $date = new Appointment;
        $date->title = $request->input('title');
        $date->date = $request->input('date');
        $date->body = $request->input('body');
        $date->id_user = Auth::user()->id;
        $date->save();

        Schedule::where('appointment_date', '=', $date->date)->update(['appointment_id' => $date->id]);

        session()->flash('status', 'Cita solicitada con exito!');

        return to_route('appointment.show');
    }

    public function destroy($id)
    {
        $appointment = Appointment::find($id);
        $appointment->delete();

        return to_route('appointment.show')->with('appointment_delete', 'Cita cancelada con exito');
    }
}
