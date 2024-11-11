<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $internship = DB::table('internship as i')
            ->select(
                'i.status as internship_status',
                'is.status as seminar_status',
                'is.schedule',
                'is.grade',
            )
            ->leftJoin('internship_seminar as is', 'is.internship_id', '=', 'i.id')
            ->where('i.student_id', '=', $user->id)
            ->first();

        return view('home.index', [
            'internship' => $internship,
        ]);
    }
}
