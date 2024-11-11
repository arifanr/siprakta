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
        $query = DB::table('internship_seminar as is')
            ->select(
                'i.title',
                'i.company_name',
                'is.*',
                'u.username',
                'u.name',
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = i.supervisor_id LIMIT 1) AS supervisor"),
            )
            ->leftJoin('internship as i', 'i.id', '=', 'is.internship_id')
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
        $query = DB::table('internship_seminar as is')
            ->select(
                'i.title',
                'i.description',
                'i.company_name',
                'i.company_address',
                'i.company_phone',
                'i.start_date',
                'i.end_date',
                'i.company_name',
                'i.transcript_id',
                'i.krs_id',
                'i.statement_id',
                'is.*',
                'u.username',
                'u.name',
                'statement_document.name as statement_name',
                'statement_document.location as statement_url',
                'registration_document.name as registration_name',
                'registration_document.location as registration_url',
                'krs_document.name as krs_name',
                'krs_document.location as krs_url',
                'transcript_document.name as transcript_name',
                'transcript_document.location as transcript_url',
                'report_document.name as report_name',
                'report_document.location as report_url',
                'assessment_document.name as assessment_name',
                'assessment_document.location as assessment_url',
                'n.message as reason',
                DB::raw("(SELECT u.id 
                    FROM users u
                    WHERE u.id = i.supervisor_id LIMIT 1) AS supervisor_id"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = i.supervisor_id LIMIT 1) AS supervisor_name"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.statement_id LIMIT 1) AS statement_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.statement_id LIMIT 1) AS statement_url"),
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
            )
            ->where('is.id', '=', $id)
            ->leftjoin('internship as i', 'i.id', '=', 'is.internship_id')
            ->leftjoin('users as u', 'u.id', '=', 'i.student_id')
            ->leftJoin('users_document as statement_document', 'statement_document.id', '=', 'i.statement_id')
            ->leftJoin('users_document as registration_document', 'registration_document.id', '=', 'is.registration_id')
            ->leftJoin('users_document as krs_document', 'krs_document.id', '=', 'i.krs_id')
            ->leftJoin('users_document as transcript_document', 'transcript_document.id', '=', 'i.transcript_id')
            ->leftJoin('users_document as report_document', 'report_document.id', '=', 'is.report_id')
            ->leftJoin('users_document as assessment_document', 'assessment_document.id', '=', 'is.assessment_sheet_id')
            ->leftjoin('notification as n',function($join) {
                $join->on('n.entity_id', '=', 'is.id')
                ->where('n.entity', '=', 'internship_seminar');
            })
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$query) {
            return redirect()
                ->route('internship-seminar.list')
                ->with('failed', 'Data tidak ditemukan');
        }

        $user = Auth::user();
        $own = null;

        if ($user->hasRole('student')) {
            $own = DB::table('internship_seminar as is')
                ->leftjoin('internship as i', 'i.id', '=', 'is.internship_id')
                ->where('is.id', '=', $id)
                ->where('i.student_id', '=', $user->id)
                ->first();

            if (!$own) {
                return redirect()
                    ->route('internship-seminar.list')
                    ->with('error', 'das');
            }

            if ($own->id != $query->internship_id) {
                return redirect()
                    ->route('internship-seminar.list')
                    ->with('error', 'da');
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
        $user = Auth::user();
        $supervisors = DB::table('users as u')
            ->select(
                'u.id',
                'u.username',
                'u.name',
                DB::raw("(SELECT ua.attribute_value
                    FROM users_attribute ua
                    WHERE u.id = ua.users_id AND ua.attribute_value = 'pembimbing_kp' LIMIT 1) AS supervisor"),
            )
            ->leftJoin('users_attribute', 'u.id', '=', 'users_attribute.users_id')
            ->whereIn('attribute_value', ['pembimbing_kp'])
            ->where('flag_delete', '=', 0)
            ->get();

        $internship = DB::table('internship as i')
            ->select(
                'i.*',
                DB::raw("(SELECT u.id
                    FROM users u
                    WHERE u.id = i.supervisor_id LIMIT 1) AS supervisor"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.statement_id LIMIT 1) AS statement_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.statement_id LIMIT 1) AS statement_url"),
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
            )
            ->where('i.student_id', '=', $user->id)
            ->where('i.status', '=', 1)
            ->first();

        if (!$internship) {
            return redirect()
                ->route('internship.list')
                ->with('error', 'Anda harus mendaftar KP terlebih dahulu dan telah disetujui oleh koordinator');
        }

        return view('internship-seminar/create', [
            'data' => $internship,
            'supervisors' => $supervisors,
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

            $registrationID = null;
            $reportID = null;
            $assessmentID = null;

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

            if (isset($request->supervisor)) {
                DB::table('internship')
                    ->where('id', '=', $request->internship_id)
                    ->update([
                        'supervisor_id' => $request->supervisor,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now('UTC')
                    ]);
            }

            DB::table('internship_seminar')->insert([
                'internship_id' => $request->internship_id,
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
        $supervisors = DB::table('users as u')
            ->select(
                'u.id',
                'u.username',
                'u.name',
                DB::raw("(SELECT ua.attribute_value
                    FROM users_attribute ua
                    WHERE u.id = ua.users_id AND ua.attribute_value = 'pembimbing_kp' LIMIT 1) AS supervisor"),
            )
            ->leftJoin('users_attribute', 'u.id', '=', 'users_attribute.users_id')
            ->whereIn('attribute_value', ['pembimbing_kp'])
            ->where('flag_delete', '=', 0)
            ->get();

        $query = DB::table('internship_seminar as is')
            ->select(
                'i.title',
                'i.description',
                'i.company_name',
                'i.company_address',
                'i.company_phone',
                'i.start_date',
                'i.end_date',
                'i.company_name',
                'i.transcript_id',
                'i.krs_id',
                'i.statement_id',
                'is.*',
                'u.username',
                'u.name',
                'statement_document.name as statement_name',
                'statement_document.location as statement_url',
                'registration_document.name as registration_name',
                'registration_document.location as registration_url',
                'krs_document.name as krs_name',
                'krs_document.location as krs_url',
                'transcript_document.name as transcript_name',
                'transcript_document.location as transcript_url',
                'report_document.name as report_name',
                'report_document.location as report_url',
                'assessment_document.name as assessment_name',
                'assessment_document.location as assessment_url',
                
                DB::raw("(SELECT u.id 
                    FROM users u
                    WHERE u.id = i.supervisor_id LIMIT 1) AS supervisor_id"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = i.supervisor_id LIMIT 1) AS supervisor_name"),
                DB::raw("(SELECT ud.name
                    FROM users_document ud
                    WHERE ud.id = i.statement_id LIMIT 1) AS statement_name"),
                DB::raw("(SELECT ud.location
                    FROM users_document ud
                    WHERE ud.id = i.statement_id LIMIT 1) AS statement_url"),
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
            )
            ->where('is.id', '=', $id)
            ->leftjoin('internship as i', 'i.id', '=', 'is.internship_id')
            ->leftjoin('users as u', 'u.id', '=', 'i.student_id')
            ->leftJoin('users_document as statement_document', 'statement_document.id', '=', 'i.statement_id')
            ->leftJoin('users_document as registration_document', 'registration_document.id', '=', 'is.registration_id')
            ->leftJoin('users_document as krs_document', 'krs_document.id', '=', 'i.krs_id')
            ->leftJoin('users_document as transcript_document', 'transcript_document.id', '=', 'i.transcript_id')
            ->leftJoin('users_document as report_document', 'report_document.id', '=', 'is.report_id')
            ->leftJoin('users_document as assessment_document', 'assessment_document.id', '=', 'is.assessment_sheet_id')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$query) {
            return redirect()
                ->route('internship-seminar.list')
                ->with('failed', 'Data tidak ditemukan');
        }

        $user = Auth::user();
        $own = null;

        if ($user->hasRole('student')) {
            $own = DB::table('internship_seminar as is')
                ->leftjoin('internship as i', 'i.id', '=', 'is.internship_id')
                ->where('is.id', '=', $id)
                ->where('i.student_id', '=', $user->id)
                ->first();

            if (!$own) {
                return redirect()
                    ->route('internship-seminar.list')
                    ->with('error', 'das');
            }

            if ($own->id != $query->internship_id) {
                return redirect()
                    ->route('internship-seminar.list')
                    ->with('error', 'da');
            }
        }

        return view('internship-seminar/edit', [
            'id' => $id,
            'data' => $query,
            'supervisors' => $supervisors,
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

            $registrationID = $request->registration_id ?? null;
            $reportID = $request->report_id ?? null;
            $assessmentID = $request->assessment_sheet_id ?? null;
            $schedule = $request->schedule_old ?? null;


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

            if ($request->schedule) {
                $schedule = Carbon::createFromFormat('d/m/Y H:i', $request->schedule, 'Asia/Jakarta')->utc();
            }

            if (isset($request->supervisor)) {
                DB::table('internship')
                    ->where('id', '=', $request->internship_id)
                    ->update([
                        'supervisor_id' => $request->supervisor,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now('UTC')
                    ]);
            }

            DB::table('internship_seminar')
                ->where('id', '=', $id)
                ->update([
                    'schedule' => $request->schedule_old ?? ($schedule ? $schedule->toDateTimeString() : null),
                    'registration_id' => $registrationID,
                    'report_id' => $reportID,
                    'assessment_sheet_id' => $assessmentID,
                    'status' => 0,
                    'grade' => $request->grade,
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
