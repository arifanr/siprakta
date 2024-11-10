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

class InternshipSeminarController extends Controller
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
        $query = DB::table('internship_seminar as i')
            ->select(
                'i.*',
                'u.username',
                'u.name',
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = i.examiner1_id LIMIT 1) AS examiner1"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = i.examiner2_id LIMIT 1) AS examiner2"),
            )
            ->leftjoin('users as u', 'u.id', '=', 'i.student_id')
            ->orderBy('created_at', 'asc');

        if ($user->hasRole('student')) {
            $query->where('student_id', '=', $user->id);
        }

        if ($request->keyword) {
            $query->where('u.username', 'ilike', "%" . $request->keyword . "%");
            $query->orWhere('u.name', 'ilike', "%" . $request->keyword . "%");
            $query->orWhere('i.title', 'ilike', "%" . $request->keyword . "%");
            $query->orWhere('i.description', 'ilike', "%" . $request->keyword . "%");
        }

        $data = $query->paginate(15);

        return view('internship-seminar/index', [
            'data' => $data,
        ]);
    }

    /**
     * DEtail
     */
    public function detail($id)
    {
        $query = DB::table('internship_seminar as i')
            ->select(
                'i.*',
                'u.username',
                'u.name',
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = i.mentor_id LIMIT 1) AS mentor_name"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = i.examiner1_id LIMIT 1) AS examiner1"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = i.examiner2_id LIMIT 1) AS examiner2"),
                DB::raw("(SELECT n.message
                    FROM notification n
                    WHERE n.entity_id = i.id
                    ORDER BY n.created_at desc LIMIT 1) AS reason"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.registration_id LIMIT 1) AS registration_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.registration_id LIMIT 1) AS registration_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.krs_id LIMIT 1) AS krs_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.krs_id LIMIT 1) AS krs_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.transcript_id LIMIT 1) AS transcript_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.transcript_id LIMIT 1) AS transcript_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.report_id LIMIT 1) AS report_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.report_id LIMIT 1) AS report_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.assessment_sheet_id LIMIT 1) AS assessment_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.assessment_sheet_id LIMIT 1) AS assessment_url"),
            )
            ->where('i.id', '=', $id)
            ->leftjoin('users as u', 'u.id', '=', 'i.student_id')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$query) {
            return redirect()
                ->route('internship-seminar.list')
                ->with('failed', '');
        }

        $user = Auth::user();
        $own = null;

        if ($user->hasRole('student')) {
            $own = DB::table('internship_seminar as i')
                ->where('student_id', '=', $user->id)
                ->first();

            if (!$own) {
                return redirect()
                    ->route('internship-seminar.list')
                    ->with('failed', '');
            }

            if ($own->id != $query->id) {
                return redirect()
                    ->route('internship-seminar.list')
                    ->with('failed', '');
            }
        }

        return view('internship-seminar/detail', [
            'data' => $query,
        ]);
    }

    /**
     * Show the application dashboard.
     */
    public function create()
    {
        $mentorInternships = DB::table('users as u')
            ->select(
                'u.id',
                'u.username',
                'u.name',
                DB::raw("(SELECT ua.attribute_value
                    FROM users_attribute ua
                    WHERE u.id = ua.users_id AND ua.attribute_value = 'pembimbing_kp' LIMIT 1) AS mentor_internship"),
            )
            ->leftJoin('users_attribute', 'u.id', '=', 'users_attribute.users_id')
            ->whereIn('attribute_value', ['pembimbing_kp'])
            ->where('flag_delete', '=', 0)
            ->get();

        $examiners = DB::table('users as u')
            ->select(
                'u.id',
                'u.username',
                'u.name',
                DB::raw("(SELECT ua.attribute_value
                    FROM users_attribute ua
                    WHERE u.id = ua.users_id AND ua.attribute_value = 'penguji_kp' LIMIT 1) AS examiner"),
            )
            ->leftJoin('users_attribute', 'u.id', '=', 'users_attribute.users_id')
            ->whereIn('attribute_value', ['penguji_kp'])
            ->where('flag_delete', '=', 0)
            ->get();

        return view('internship-seminar/create', [
            'mentorInternships' => $mentorInternships,
            'examiners' => $examiners,
        ]);
    }

    /**
     * 
     */
    public function save(Request $request)
    {
        $rules = [
            'transcript' => 'required|file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
            'krs' => 'required|file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
            'registration' => 'file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
            'report' => 'required|file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
            'assessment_sheet' => 'required|file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
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

            $path = 'save_folder/internship_seminar';

            if (!file_exists($path)) {
                mkdir($path, 0775, true);
            }

            $mentorID = null;
            $registrationID = null;
            $trancriptID = null;
            $krsID = null;
            $reportID = null;
            $assessmentID = null;

            if (isset($request->mentor)) {
                $mentorID = $request->mentor;
            }

            if (isset($request->transcript)) {
                $file = $request->file('transcript');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $trancriptID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'transcript',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            if (isset($request->krs)) {
                $file = $request->file('krs');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $krsID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'krs',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            if (isset($request->registration)) {
                $file = $request->file('registration');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $registrationID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'internship_seminar_registration',
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
                    'type' => 'internship_seminar_report',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            if (isset($request->assessment_sheet)) {
                $file = $request->file('assessment_sheet');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $assessmentID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'internship_seminar_assessment_sheet',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            $start_date = Carbon::createFromFormat('d/m/Y', $request->start_date);
            $end_date = Carbon::createFromFormat('d/m/Y', $request->end_date);

            DB::table('internship_seminar')->insert([
                'title' => $request->title,
                'description' => $request->description,
                'company_name' => $request->company_name,
                'company_address' => $request->company_address,
                'company_phone' => $request->company_phone,
                'start_date' => $start_date->toDateTimeString(),
                'end_date' => $end_date->toDateTimeString(),
                'student_id' => Auth::user()->id,
                'mentor_id' => $mentorID,
                'transcript_id' => $trancriptID,
                'krs_id' => $krsID,
                'registration_id' => $registrationID,
                'report_id' => $reportID,
                'assessment_sheet_id' => $assessmentID,
                'status' => 0,
                'created_by' => Auth::user()->username,
                'created_at' => Carbon::now('UTC')
            ]);

            DB::commit();

            return redirect()
                ->route('internship-seminar.list')
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
        $mentorInternships = DB::table('users as u')
            ->select(
                'u.id',
                'u.username',
                'u.name',
                DB::raw("(SELECT ua.attribute_value
                    FROM users_attribute ua
                    WHERE u.id = ua.users_id AND ua.attribute_value = 'pembimbing_kp' LIMIT 1) AS mentor_internship"),
            )
            ->leftJoin('users_attribute', 'u.id', '=', 'users_attribute.users_id')
            ->whereIn('attribute_value', ['pembimbing_kp'])
            ->where('flag_delete', '=', 0)
            ->get();

        $examiners = DB::table('users as u')
            ->select(
                'u.id',
                'u.username',
                'u.name',
                DB::raw("(SELECT ua.attribute_value
                    FROM users_attribute ua
                    WHERE u.id = ua.users_id AND ua.attribute_value = 'penguji_kp' LIMIT 1) AS examiner"),
            )
            ->leftJoin('users_attribute', 'u.id', '=', 'users_attribute.users_id')
            ->whereIn('attribute_value', ['penguji_kp'])
            ->where('flag_delete', '=', 0)
            ->get();

        $query = DB::table('internship_seminar as i')
            ->select(
                'i.*',
                'u.username',
                'u.name',
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = i.mentor_id LIMIT 1) AS mentor_name"),
                DB::raw("(SELECT u.id 
                    FROM users u
                    WHERE u.id = i.examiner1_id LIMIT 1) AS examiner1_id"),
                DB::raw("(SELECT u.id 
                    FROM users u
                    WHERE u.id = i.examiner2_id LIMIT 1) AS examiner2_id"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = i.examiner1_id LIMIT 1) AS examiner1"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = i.examiner2_id LIMIT 1) AS examiner2"),
                DB::raw("(SELECT n.message
                    FROM notification n
                    WHERE n.entity_id = i.id
                    ORDER BY n.created_at desc LIMIT 1) AS reason"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.registration_id LIMIT 1) AS registration_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.registration_id LIMIT 1) AS registration_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.krs_id LIMIT 1) AS krs_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.krs_id LIMIT 1) AS krs_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.transcript_id LIMIT 1) AS transcript_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.transcript_id LIMIT 1) AS transcript_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.report_id LIMIT 1) AS report_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.report_id LIMIT 1) AS report_url"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.assessment_sheet_id LIMIT 1) AS assessment_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.assessment_sheet_id LIMIT 1) AS assessment_url"),
            )
            ->where('i.id', '=', $id)
            ->leftjoin('users as u', 'u.id', '=', 'i.student_id')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$query) {
            return redirect()
                ->route('internship-seminar.list')
                ->with('failed', '');
        }

        $user = Auth::user();
        $own = null;

        if ($user->hasRole('student')) {
            $own = DB::table('internship_seminar as i')
                ->where('student_id', '=', $user->id)
                ->first();

            if (!$own) {
                return redirect()
                    ->route('internship-seminar.list')
                    ->with('failed', '');
            }

            if ($own->id != $query->id) {
                return redirect()
                    ->route('internship-seminar.list')
                    ->with('failed', '');
            }
        }

        return view('internship-seminar/edit', [
            'id' => $id,
            'data' => $query,
            'mentorInternships' => $mentorInternships,
            'examiners' => $examiners,
        ]);
    }

    /**
     * 
     */
    public function update($id, Request $request)
    {
        $rules = [
            'transcript' => 'file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
            'krs' => 'file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
            'registration' => 'file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
            'report' => 'file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
            'assessment_sheet' => 'file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
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

            $path = 'save_folder/internship_seminar';

            if (!file_exists($path)) {
                mkdir($path, 0775, true);
            }

            $mentorID = $request->mentor_id ?? null;
            $examiner1ID = $request->examiner1_id ?? null;
            $examiner2ID = $request->examiner2_id ?? null;
            $registrationID = $request->registration_id ?? null;
            $trancriptID = $request->transcript_id ?? null;
            $krsID = $request->krs_id ?? null;
            $reportID = $request->report_id ?? null;
            $assessmentID = $request->assessment_sheet_id ?? null;
            $schedule = $request->schedule_old ?? null;

            if (isset($request->mentor)) {
                $mentorID = $request->mentor;
            }

            if (isset($request->examiner1)) {
                $examiner1ID = $request->examiner1;
            }

            if (isset($request->examiner2)) {
                $examiner2ID = $request->examiner2;
            }

            if (isset($request->transcript)) {
                $file = $request->file('transcript');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $trancriptID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'transcript',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            if (isset($request->krs)) {
                $file = $request->file('krs');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $krsID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'krs',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            if (isset($request->registration)) {
                $file = $request->file('registration');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $registrationID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'internship_seminar_registration',
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
                    'type' => 'internship_seminar_report',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            if (isset($request->assessment_sheet)) {
                $file = $request->file('assessment_sheet');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $assessmentID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'internship_seminar_assessment_sheet',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            $start_date = Carbon::createFromFormat('d/m/Y H:i:s', $request->start_date . ' 00:00:00');
            $end_date = Carbon::createFromFormat('d/m/Y H:i:s', $request->end_date . ' 00:00:00');

            if ($request->schedule) {
                $schedule = Carbon::createFromFormat('d/m/Y H:i', $request->schedule, 'Asia/Jakarta')->utc();
            }

            DB::table('internship_seminar')
                ->where('id', '=', $id)
                ->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'company_name' => $request->company_name,
                    'company_address' => $request->company_address,
                    'company_phone' => $request->company_phone,
                    'start_date' => $start_date->toDateTimeString(),
                    'end_date' => $end_date->toDateTimeString(),
                    'schedule' => $request->schedule_old ?? ($schedule ? $schedule->toDateTimeString() : null),
                    'mentor_id' => $mentorID,
                    'examiner1_id' => $examiner1ID,
                    'examiner2_id' => $examiner2ID,
                    'transcript_id' => $trancriptID,
                    'krs_id' => $krsID,
                    'registration_id' => $registrationID,
                    'report_id' => $reportID,
                    'assessment_sheet_id' => $assessmentID,
                    'status' => 0,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now('UTC')
                ]);

            DB::commit();

            return redirect()
                ->route('internship-seminar.list')
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

            DB::table('internship_seminar')
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
                    'entity' => 'internship_seminar',
                    'entity_id' => $id,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);

            DB::commit();

            return redirect()
                ->route('internship-seminar.list')
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

            DB::table('internship_seminar')
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
                    'entity' => 'internship_seminar',
                    'entity_id' => $id,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);

            DB::commit();

            return redirect()
                ->route('internship-seminar.list')
                ->with('success', 'Seminar status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload data: ' . $e->getMessage())->withInput();
        }
    }
}
