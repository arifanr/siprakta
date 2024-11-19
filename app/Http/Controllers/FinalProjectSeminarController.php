<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FinalProjectSeminarController extends Controller
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
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DB::table('final_project_seminar as fps')
            ->select(
                'fp.title',
                'fps.*',
                'u.username',
                'u.name',
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fp.supervisor1_id LIMIT 1) AS supervisor_1"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fp.supervisor1_id LIMIT 1) AS supervisor_2"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fps.examiner1_id LIMIT 1) AS examiner_1"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fps.examiner2_id LIMIT 1) AS examiner_2"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fps.examiner3_id LIMIT 1) AS examiner_3"),
            )
            ->leftJoin('final_project as fp', 'fp.id', '=', 'fps.final_project_id')
            ->leftjoin('users as u', 'u.id', '=', 'fp.student_id')
            ->orderBy('created_at', 'asc');

        if ($user->hasRole('student')) {
            $query->where('student_id', '=', $user->id);
        }

        if ($request->keyword) {
            $query->where('u.username', 'ilike', "%" . $request->keyword . "%");
            $query->orWhere('u.name', 'ilike', "%" . $request->keyword . "%");
            $query->orWhere('fps.title', 'ilike', "%" . $request->keyword . "%");
            $query->orWhere('fps.description', 'ilike', "%" . $request->keyword . "%");
        }

        $data = $query->paginate(15);

        return view('final-project-seminar/index', [
            'data' => $data,
        ]);
    }

    /**
     * DEtail
     */
    public function detail($id)
    {
        $query = DB::table('final_project_seminar as fps')
            ->select(
                'fp.title',
                'fp.description',
                'fp.transcript_id',
                'fp.krs_id',
                'fps.*',
                'u.username',
                'u.name',
                'n.message as reason',
                DB::raw("(SELECT u.name
                    FROM users u
                    WHERE u.id = fp.supervisor1_id LIMIT 1) AS supervisor_1"),
                DB::raw("(SELECT u.name
                    FROM users u
                    WHERE u.id = fp.supervisor2_id LIMIT 1) AS supervisor_2"),
                DB::raw("(SELECT u.id 
                    FROM users u
                    WHERE u.id = fps.examiner1_id LIMIT 1) AS examiner1_id"),
                DB::raw("(SELECT u.id 
                    FROM users u
                    WHERE u.id = fps.examiner2_id LIMIT 1) AS examiner2_id"),
                DB::raw("(SELECT u.id 
                    FROM users u
                    WHERE u.id = fps.examiner3_id LIMIT 1) AS examiner3_id"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fps.examiner1_id LIMIT 1) AS examiner1"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fps.examiner2_id LIMIT 1) AS examiner2"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fps.examiner3_id LIMIT 1) AS examiner3"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fps.registration_id LIMIT 1) AS registration_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fps.registration_id LIMIT 1) AS registration_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fp.krs_id LIMIT 1) AS krs_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fp.krs_id LIMIT 1) AS krs_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fp.transcript_id LIMIT 1) AS transcript_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fp.transcript_id LIMIT 1) AS transcript_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fps.report_id LIMIT 1) AS report_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fps.report_id LIMIT 1) AS report_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fps.report_id LIMIT 1) AS report_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fps.report_id LIMIT 1) AS report_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fp.proposal_id LIMIT 1) AS proposal_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fp.proposal_id LIMIT 1) AS proposal_url"),
            )
            ->where('fps.id', '=', $id)
            ->leftjoin('final_project as fp', 'fp.id', '=', 'fps.final_project_id')
            ->leftjoin('users as u', 'u.id', '=', 'fp.student_id')
            ->leftjoin('notification as n', function ($join) {
                $join->on('n.entity_id', '=', 'fps.id')
                    ->where('n.entity', '=', 'final_project_seminar');
            })
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$query) {
            return redirect()
                ->route('finalproject-seminar.list')
                ->with('error', 'data tidak ditemukan');
        }

        $user = Auth::user();
        $own = null;

        if ($user->hasRole('student')) {
            $own = DB::table('final_project_seminar as fps')
                ->leftjoin('final_project as fp', 'fp.id', '=', 'fps.final_project_id')
                ->where('fps.id', '=', $id)
                ->where('student_id', '=', $user->id)
                ->first();

            if (!$own) {
                return redirect()
                    ->route('finalproject-seminar.list')
                    ->with('error', '403 Forbidden');
            }

            if ($own->id != $query->final_project_id) {
                return redirect()
                    ->route('finalproject-seminar.list')
                    ->with('error', '403 Forbidden');
            }
        }

        return view('final-project-seminar/detail', [
            'data' => $query,
        ]);
    }

    /**
     * Show the application dashboard.
     */
    public function create()
    {
        $user = Auth::user();

        $supervisors = DB::table('users as u')
            ->select(
                'u.id',
                'u.username',
                'u.name',
                DB::raw("(SELECT ua.attribute_value
                    FROM users_attribute ua
                    WHERE u.id = ua.users_id AND ua.attribute_value = 'pembimbing_ta' LIMIT 1) AS supervisor"),
            )
            ->leftJoin('users_attribute', 'u.id', '=', 'users_attribute.users_id')
            ->whereIn('attribute_value', ['pembimbing_ta'])
            ->where('flag_delete', '=', 0)
            ->get();

        $examiners = DB::table('users as u')
            ->select(
                'u.id',
                'u.username',
                'u.name',
                DB::raw("(SELECT ua.attribute_value
                    FROM users_attribute ua
                    WHERE u.id = ua.users_id AND ua.attribute_value = 'penguji_ta' LIMIT 1) AS examiner"),
            )
            ->leftJoin('users_attribute', 'u.id', '=', 'users_attribute.users_id')
            ->whereIn('attribute_value', ['penguji_ta'])
            ->where('flag_delete', '=', 0)
            ->get();

        $finalproject = DB::table('final_project as fp')
            ->select(
                'fp.*',
                DB::raw("(SELECT u.id
                    FROM users u
                    WHERE u.id = fp.supervisor1_id LIMIT 1) AS supervisor_1"),
                DB::raw("(SELECT u.id
                    FROM users u
                    WHERE u.id = fp.supervisor2_id LIMIT 1) AS supervisor_2"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fp.proposal_id LIMIT 1) AS proposal_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fp.proposal_id LIMIT 1) AS proposal_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fp.krs_id LIMIT 1) AS krs_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fp.krs_id LIMIT 1) AS krs_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fp.transcript_id LIMIT 1) AS transcript_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fp.transcript_id LIMIT 1) AS transcript_url"),
            )
            ->where('fp.student_id', '=', $user->id)
            ->where('fp.status', '=', 1)
            ->first();

        if (!$finalproject) {
            return redirect()
                ->route('finalproject.list')
                ->with('error', 'Anda harus mendaftar Tugas Akhir terlebih dahulu dan telah disetujui oleh koordinator');
        }

        return view('final-project-seminar/create', [
            'data' => $finalproject,
            'supervisors' => $supervisors,
            'examiners' => $examiners,
        ]);
    }

    /**
     * 
     */
    public function save(Request $request)
    {
        $rules = [
            'registration' => 'file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
            'report' => 'required|file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()
                ->withErrors($validator)
                ->with(['error' => $errors->toJson()])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $path = 'save_folder/final_project_seminar';

            if (!file_exists($path)) {
                mkdir($path, 0775, true);
            }

            $supervisor1ID = null;
            $supervisor2ID = null;
            $registrationID = null;
            $reportID = null;

            if (isset($request->supervisor_1)) {
                $supervisor1ID = $request->supervisor_1;
            }

            if (isset($request->supervisor_1)) {
                $supervisor2ID = $request->supervisor_2;
            }

            if (isset($request->registration)) {
                $file = $request->file('registration');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $registrationID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'final_project_seminar_registration',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            if (isset($request->report)) {
                $file = $request->file('report');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $reportID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'final_project_seminar_report',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            DB::table('final_project')
                ->where('id', '=', $request->final_project_id)
                ->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'supervisor1_id' => $supervisor1ID,
                    'supervisor2_id' => $supervisor2ID,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now('UTC')
                ]);

            DB::table('final_project_seminar')->insert([
                'final_project_id' => $request->final_project_id,
                'registration_id' => $registrationID,
                'report_id' => $reportID,
                'status' => 0,
                'created_by' => Auth::user()->username,
                'created_at' => Carbon::now('UTC')
            ]);

            DB::commit();

            return redirect()
                ->route('finalproject-seminar.list')
                ->with('success', 'Seminar data and files uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 
     */
    public function edit($id, Request $request)
    {
        $supervisors = DB::table('users as u')
            ->select(
                'u.id',
                'u.username',
                'u.name',
                DB::raw("(SELECT ua.attribute_value
                    FROM users_attribute ua
                    WHERE u.id = ua.users_id AND ua.attribute_value = 'pembimbing_ta' LIMIT 1) AS supervisor_internship"),
            )
            ->leftJoin('users_attribute', 'u.id', '=', 'users_attribute.users_id')
            ->whereIn('attribute_value', ['pembimbing_ta'])
            ->where('flag_delete', '=', 0)
            ->get();

        $examiners = DB::table('users as u')
            ->select(
                'u.id',
                'u.username',
                'u.name',
                DB::raw("(SELECT ua.attribute_value
                    FROM users_attribute ua
                    WHERE u.id = ua.users_id AND ua.attribute_value = 'penguji_ta' LIMIT 1) AS examiner"),
            )
            ->leftJoin('users_attribute', 'u.id', '=', 'users_attribute.users_id')
            ->whereIn('attribute_value', ['penguji_ta'])
            ->where('flag_delete', '=', 0)
            ->get();

        $query = DB::table('final_project_seminar as fps')
            ->select(
                'fp.title',
                'fp.description',
                'fp.transcript_id',
                'fp.krs_id',
                'fps.*',
                'u.username',
                'u.name',
                DB::raw("(SELECT u.id
                    FROM users u
                    WHERE u.id = fp.supervisor1_id LIMIT 1) AS supervisor1_id"),
                DB::raw("(SELECT u.id
                    FROM users u
                    WHERE u.id = fp.supervisor2_id LIMIT 1) AS supervisor2_id"),
                DB::raw("(SELECT u.id 
                    FROM users u
                    WHERE u.id = fps.examiner1_id LIMIT 1) AS examiner1_id"),
                DB::raw("(SELECT u.id 
                    FROM users u
                    WHERE u.id = fps.examiner2_id LIMIT 1) AS examiner2_id"),
                DB::raw("(SELECT u.id 
                    FROM users u
                    WHERE u.id = fps.examiner3_id LIMIT 1) AS examiner3_id"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fps.examiner1_id LIMIT 1) AS examiner1"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fps.examiner2_id LIMIT 1) AS examiner2"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fps.examiner3_id LIMIT 1) AS examiner3"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fps.registration_id LIMIT 1) AS registration_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fps.registration_id LIMIT 1) AS registration_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fp.krs_id LIMIT 1) AS krs_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fp.krs_id LIMIT 1) AS krs_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fp.transcript_id LIMIT 1) AS transcript_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fp.transcript_id LIMIT 1) AS transcript_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fps.report_id LIMIT 1) AS report_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fps.report_id LIMIT 1) AS report_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fps.report_id LIMIT 1) AS report_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fps.report_id LIMIT 1) AS report_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = fp.proposal_id LIMIT 1) AS proposal_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = fp.proposal_id LIMIT 1) AS proposal_url"),
            )
            ->where('fps.id', '=', $id)
            ->leftjoin('final_project as fp', 'fp.id', '=', 'fps.final_project_id')
            ->leftjoin('users as u', 'u.id', '=', 'fp.student_id')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$query) {
            return redirect()
                ->route('finalproject-seminar.list')
                ->with('error', 'data tidak ditemukan');
        }

        $user = Auth::user();
        $own = null;

        if ($user->hasRole('student')) {
            $own = DB::table('final_project_seminar as fps')
                ->leftjoin('final_project as fp', 'fp.id', '=', 'fps.final_project_id')
                ->where('fps.id', '=', $id)
                ->where('student_id', '=', $user->id)
                ->first();

            if (!$own) {
                return redirect()
                    ->route('finalproject-seminar.list')
                    ->with('error', '403 Forbidden');
            }

            if ($own->id != $query->final_project_id) {
                return redirect()
                    ->route('finalproject-seminar.list')
                    ->with('error', '403 Forbidden');
            }
        }

        return view('final-project-seminar/edit', [
            'id' => $id,
            'data' => $query,
            'supervisors' => $supervisors,
            'examiners' => $examiners,
        ]);
    }

    /**
     * 
     */
    public function update($id, Request $request)
    {
        $rules = [
            'registration' => 'file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
            'report' => 'file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()
                ->withErrors($validator)
                ->with(['error' => $errors->toJson()])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $path = 'save_folder/final_project_seminar';

            if (!file_exists($path)) {
                mkdir($path, 0775, true);
            }

            $supervisor1ID = $request->supervisor1_id ?? null;
            $supervisor2ID = $request->supervisor2_id ?? null;
            $examiner1ID = $request->examiner1_id ?? null;
            $examiner2ID = $request->examiner2_id ?? null;
            $examiner3ID = $request->examiner3_id ?? null;
            $registrationID = $request->registration_id ?? null;
            $reportID = $request->report_id ?? null;
            $schedule = $request->schedule_old ?? null;

            if (isset($request->supervisor_1)) {
                $supervisor1ID = $request->supervisor_1;
            }

            if (isset($request->supervisor_2)) {
                $supervisor2ID = $request->supervisor_2;
            }

            if (isset($request->examiner1)) {
                $examiner1ID = $request->examiner1;
            }

            if (isset($request->examiner2)) {
                $examiner2ID = $request->examiner2;
            }

            if (isset($request->examiner3)) {
                $examiner3ID = $request->examiner3;
            }

            if (isset($request->registration)) {
                $file = $request->file('registration');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $registrationID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'final_project_seminar_registration',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            if (isset($request->report)) {
                $file = $request->file('report');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $reportID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'final_project_seminar_report',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            if ($request->schedule) {
                $schedule = Carbon::createFromFormat('d/m/Y H:i', $request->schedule, 'Asia/Jakarta')->utc();
            }

            DB::table('final_project')
                ->where('id', '=', $request->final_project_id)
                ->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'supervisor1_id' => $supervisor1ID,
                    'supervisor2_id' => $supervisor2ID,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now('UTC')
                ]);

            DB::table('final_project_seminar')
                ->where('id', '=', $id)
                ->update([
                    'schedule' => $request->schedule_old ?? ($schedule ? $schedule->toDateTimeString() : null),
                    'registration_id' => $registrationID,
                    'report_id' => $reportID,
                    'examiner1_id' => $examiner1ID,
                    'examiner2_id' => $examiner2ID,
                    'examiner3_id' => $examiner3ID,
                    'status' => 0,
                    'grade_1' => $request->grade_1,
                    'grade_2' => $request->grade_2,
                    'grade_3' => $request->grade_3,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now('UTC')
                ]);

            DB::commit();

            return redirect()
                ->route('finalproject-seminar.list')
                ->with('success', 'Seminar data and files uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 
     */
    public function approve($id)
    {
        try {
            DB::beginTransaction();

            DB::table('final_project_seminar')
                ->where('id', $id)
                ->update([
                    'status' => 1,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now('UTC')
                ]);

            DB::table('notification')
                ->insert([
                    'type' => 'approve',
                    'title' => 'Disetujui',
                    'message' => '',
                    'entity' => 'final_project_seminar',
                    'entity_id' => $id,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);

            DB::commit();

            return redirect()
                ->route('finalproject-seminar.list')
                ->with('success', 'Seminar status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 
     */
    public function deny($id, Request $request)
    {
        try {
            DB::beginTransaction();

            DB::table('final_project_seminar')
                ->where('id', $id)
                ->update([
                    'status' => 2,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now('UTC')
                ]);

            DB::table('notification')
                ->insert([
                    'type' => 'deny',
                    'title' => 'Ditolak',
                    'message' => $request->reason,
                    'entity' => 'final_project_seminar',
                    'entity_id' => $id,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);

            DB::commit();

            return redirect()
                ->route('finalproject-seminar.list')
                ->with('success', 'Seminar status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 
     */
    public function reset($id)
    {
        try {
            DB::beginTransaction();

            DB::table('final_project_seminar')
                ->where('id', $id)
                ->update([
                    'status' => 0,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now('UTC')
                ]);

            DB::table('notification')
                ->insert([
                    'type' => 'reset',
                    'title' => 'Reset Status',
                    'message' => '',
                    'entity' => 'final_project_seminar',
                    'entity_id' => $id,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);

            DB::commit();

            return redirect()
                ->route('finalproject-seminar.list')
                ->with('success', 'Seminar status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload data: ' . $e->getMessage())->withInput();
        }
    }
}
