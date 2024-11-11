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
                'i.status as status',
                'is.status as seminar_status',
                'is.schedule',
                'is.grade',
            )
            ->leftJoin('internship_seminar as is', 'is.internship_id', '=', 'i.id')
            ->where('i.student_id', '=', $user->id)
            ->first();

        $finalproject = DB::table('final_project as fp')
            ->select(
                'fp.status as status',
                'fps.status as seminar_status',
                'fps.schedule',
                'fps.grade',
            )
            ->leftJoin('final_project_seminar as fps', 'fps.final_project_id', '=', 'fp.id')
            ->where('fp.student_id', '=', $user->id)
            ->first();

        return view('home.index', [
            'internship' => $internship,
            'finalproject' => $finalproject,
        ]);
    }
}
