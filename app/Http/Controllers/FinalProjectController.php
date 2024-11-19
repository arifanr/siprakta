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

class FinalProjectController extends Controller
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

        $query = DB::table('final_project as fp')
            ->select(
                'fp.*',
                'u.username',
                'u.name',
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fp.supervisor1_id LIMIT 1) AS supervisor_1"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fp.supervisor2_id LIMIT 1) AS supervisor_2"),
            )
            ->leftjoin('users as u', 'u.id', '=', 'fp.student_id')

            ->orderBy('created_at', 'asc');

        if ($user->hasRole('student')) {
            $query->where('student_id', '=', $user->id);
        }

        if ($request->keyword) {
            $query->where('u.username', 'ilike', "%" . $request->keyword . "%");
            $query->orWhere('u.name', 'ilike', "%" . $request->keyword . "%");
            $query->orWhere('fp.title', 'ilike', "%" . $request->keyword . "%");
            $query->orWhere('fp.description', 'ilike', "%" . $request->keyword . "%");
        }

        $data = $query->paginate(15);

        return view('final-project/index', [
            'data' => $data,
        ]);
    }

    /**
     * DEtail
     */
    public function detail($id)
    {
        $query = DB::table('final_project as fp')
            ->select(
                'fp.*',
                'u.username',
                'u.name',
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fp.supervisor1_id LIMIT 1) AS supervisor_1"),
                DB::raw("(SELECT u.name 
                    FROM users u
                    WHERE u.id = fp.supervisor2_id LIMIT 1) AS supervisor_2"),
                DB::raw("(SELECT n.message
                    FROM notification n
                    WHERE n.entity_id = fp.id and n.entity = 'final_project'
                    ORDER BY n.created_at desc LIMIT 1) AS reason"),
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
            ->where('fp.id', '=', $id)
            ->leftjoin('users as u', 'u.id', '=', 'fp.student_id')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$query) {
            return redirect()
                ->route('finalproject.list')
                ->with('error', '');
        }

        $user = Auth::user();
        $own = null;

        if ($user->hasRole('student')) {
            $own = DB::table('final_project as fp')
                ->where('student_id', '=', $user->id)
                ->first();

            if (!$own) {
                return redirect()
                    ->route('finalproject.list')
                    ->with('error', '');
            }

            if ($own->id != $query->id) {
                return redirect()
                    ->route('finalproject.list')
                    ->with('error', '');
            }
        }

        return view('final-project/detail', [
            'data' => $query,
        ]);
    }

    /**
     * Show the application dashboard.
     */
    public function create()
    {
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

        return view('final-project/create', [
            'supervisors' => $supervisors,
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
            'proposal' => 'required|file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
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

            $path = 'save_folder/final_project';

            if (!file_exists($path)) {
                mkdir($path, 0775, true);
            }

            $supervisor1ID = null;
            $supervisor2ID = null;
            $proposalID = null;
            $trancriptID = null;
            $krsID = null;

            if (isset($request->supervisor_1)) {
                $supervisor1ID = $request->supervisor_1;
            }

            if (isset($request->supervisor_2)) {
                $supervisor2ID = $request->supervisor_2;
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

            if (isset($request->proposal)) {
                $file = $request->file('proposal');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $proposalID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'final_project_proposal',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            DB::table('final_project')->insert([
                'title' => $request->title,
                'description' => $request->description,
                'student_id' => Auth::user()->id,
                'supervisor1_id' => $supervisor1ID,
                'supervisor2_id' => $supervisor2ID,
                'transcript_id' => $trancriptID,
                'krs_id' => $krsID,
                'proposal_id' => $proposalID,
                'status' => 0,
                'created_by' => Auth::user()->username,
                'created_at' => Carbon::now('UTC')
            ]);

            DB::commit();

            return redirect()
                ->route('finalproject.list')
                ->with('success', 'Final Projects data and files uploaded successfully.');
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
            ->whereIn('attribute_value', ['pembimbing_ta'])
            ->where('flag_delete', '=', 0)
            ->get();

        $query = DB::table('final_project as fp')
            ->select(
                'fp.*',
                'u.username',
                'u.name',
                DB::raw("(SELECT u.id 
                    FROM users u
                    WHERE u.id = fp.supervisor1_id LIMIT 1) AS supervisor1_id"),
                DB::raw("(SELECT u.id 
                    FROM users u
                    WHERE u.id = fp.supervisor2_id LIMIT 1) AS supervisor2_id"),
                DB::raw("(SELECT n.message
                    FROM notification n
                    WHERE n.entity_id = fp.id and n.entity = 'final_project'
                    ORDER BY n.created_at desc LIMIT 1) AS reason"),
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
            ->where('fp.id', '=', $id)
            ->leftjoin('users as u', 'u.id', '=', 'fp.student_id')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$query) {
            return redirect()
                ->route('finalproject.list')
                ->with('error', '');
        }

        $user = Auth::user();
        $own = null;

        if ($user->hasRole('student')) {
            $own = DB::table('final_project as fp')
                ->where('student_id', '=', $user->id)
                ->first();

            if (!$own) {
                return redirect()
                    ->route('finalproject.list')
                    ->with('error', '');
            }

            if ($own->id != $query->id) {
                return redirect()
                    ->route('finalproject.list')
                    ->with('error', '');
            }
        }

        return view('final-project/edit', [
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
            'transcript' => 'file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
            'krs' => 'file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
            'proposal' => 'file|mimes:webp,jpeg,jpg,png,pdf|max:10240',
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

            $path = 'save_folder/final_project';

            if (!file_exists($path)) {
                mkdir($path, 0775, true);
            }

            $supervisor1ID = $request->supervisor1_id ?? null;
            $supervisor2ID = $request->supervisor2_id ?? null;
            $proposalID = $request->proposal_id ?? null;
            $trancriptID = $request->transcript_id ?? null;
            $krsID = $request->krs_id ?? null;

            if (isset($request->supervisor_1)) {
                $supervisor1ID = $request->supervisor_1;
            }

            if (isset($request->supervisor_2)) {
                $supervisor2ID = $request->supervisor_2;
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

            if (isset($request->proposal)) {
                $file = $request->file('proposal');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(20) . '.' . $extension;

                file_put_contents($path . '/' . $fileName, file_get_contents($file));

                $proposalID = DB::table('users_document')->insertGetId([
                    'name' => $originalName,
                    'type' => 'final_project_proposal',
                    'location' => $path . '/' . $fileName,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);
            }

            DB::table('final_project')
                ->where('id', '=', $id)
                ->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'supervisor1_id' => $supervisor1ID,
                    'supervisor2_id' => $supervisor2ID,
                    'transcript_id' => $trancriptID,
                    'krs_id' => $krsID,
                    'proposal_id' => $proposalID,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now('UTC')
                ]);

            DB::commit();

            return redirect()
                ->route('finalproject.list')
                ->with('success', 'Final Project data and files uploaded successfully.');
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

            DB::table('final_project')
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
                    'entity' => 'final_project',
                    'entity_id' => $id,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);

            DB::commit();

            return redirect()
                ->route('finalproject.list')
                ->with('success', 'Final Project status updated successfully.');
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

            DB::table('final_project')
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
                    'entity' => 'final_project',
                    'entity_id' => $id,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);

            DB::commit();

            return redirect()
                ->route('finalproject.list')
                ->with('success', 'Final Project status updated successfully.');
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

            DB::table('final_project')
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
                    'entity' => 'final_project',
                    'entity_id' => $id,
                    'users_id' => Auth::user()->id,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now('UTC')
                ]);

            DB::commit();

            return redirect()
                ->route('finalproject.list')
                ->with('success', 'Final Project status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload data: ' . $e->getMessage())->withInput();
        }
    }
}
