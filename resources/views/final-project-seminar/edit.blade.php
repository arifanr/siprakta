@extends('adminlte::page')

@section('title', 'Kerja Praktik')

@section('content_header')
    <h1><b>Kerja Praktik</b></h1>
@stop

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-12 col-md-9">
            <div class="card card-info">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Ajukan Kerja Praktik</h3>
                    <div class="card-tools text-right">
                        <a href="{{ route('finalproject-seminar.list') }}" class="btn btn-dark btn-sm">
                            Back
                        </a>
                    </div>
                </div>
                <form class="form-horizontal" action="{{ route('finalproject-seminar.update', $id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    {{ method_field('patch') }}
                    <input type="hidden" name="final_project_id" value="{{ $data->final_project_id }}">
                    <input type="hidden" name="transcript_id" value="{{ $data->transcript_id }}">
                    <input type="hidden" name="krs_id" value="{{ $data->krs_id }}">
                    <input type="hidden" name="registration_id" value="{{ $data->registration_id }}">
                    <input type="hidden" name="report_id" value="{{ $data->report_id }}">
                    <input type="hidden" name="supervisor1_id" value="{{ $data->supervisor1_id }}">
                    <input type="hidden" name="supervisor2_id" value="{{ $data->supervisor2_id }}">
                    <input type="hidden" name="examiner1_id" value="{{ $data->examiner1_id }}">
                    <input type="hidden" name="examiner2_id" value="{{ $data->examiner2_id }}">
                    <input type="hidden" name="examiner3_id" value="{{ $data->examiner3_id }}">
                    <input type="hidden" name="schedule_old" value="{{ $data->schedule }}">
                    <div class="card-body">
                        @if (!Auth::user()->hasRole('student'))
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">
                                    Jadwal Seminar
                                </label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="schedule"
                                            data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy HH:MM"
                                            data-mask
                                            value="{{ $data->schedule? \Carbon\Carbon::parse($data->schedule)->timezone(session('timezone', 'Asia/Jakarta'))->format('d/m/Y H:i'): '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">Penguji 1</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2bs4 w-full" name="examiner1">
                                        <option value="">-- Penguji TA --</option>
                                        @foreach ($examiners as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $data->examiner1_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">Nilai Penguji 1</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="grade_1" value="{{ $data->grade_1 }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">Penguji 2</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2bs4 w-full" name="examiner2">
                                        <option value="">-- Penguji TA --</option>
                                        @foreach ($examiners as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $data->examiner2_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">Nilai Penguji 2</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="grade_2" value="{{ $data->grade_2 }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">Penguji 3</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2bs4 w-full" name="examiner3">
                                        <option value="">-- Penguji TA --</option>
                                        @foreach ($examiners as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $data->examiner3_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">Nilai Penguji 3</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="grade_3" value="{{ $data->grade_3 }}">
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Pembimbing 1
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control select2bs4 w-full" name="supervisor_1"
                                    {{ Auth::user()->hasRole('student') ? 'disabled' : 'required' }}>
                                    <option value="">-- Pembimbing TA --</option>
                                    @foreach ($supervisors as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $data->supervisor1_id == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Pembimbing 2
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control select2bs4 w-full" name="supervisor_2"
                                    {{ Auth::user()->hasRole('student') ? 'disabled' : '' }}>
                                    <option value="">-- Pembimbing TA --</option>
                                    @foreach ($supervisors as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $data->supervisor2_id == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Judul
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="title" value="{{ $data->title }}"
                                    required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Deskripsi</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="description" rows="5">{{ $data->description }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Formulir Pendaftaran</label>
                            <div class="col-sm-9">
                                <div class="input-group mb-2">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile"
                                            name="registration" accept=".pdf, .webp, .png, .jpeg, .jpg">
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                </div>
                                @if ($data->registration_url)
                                    @if (explode('.', $data->registration_url)[1] != 'pdf')
                                        <a href="{{ asset($data->registration_url) }}" target="_blank">
                                            <img src="{{ asset($data->registration_url) }}" alt=""
                                                height="150px">
                                        </a>
                                    @else
                                        <a href="{{ asset($data->registration_url) }}" target="_blank">
                                            {{ $data->registration_name }}
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Laporan TA
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile"
                                            name="report" accept=".pdf, .webp, .png, .jpeg, .jpg">
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                </div>
                                @if ($data->report_url)
                                    @if (explode('.', $data->report_url)[1] != 'pdf')
                                        <a href="{{ asset($data->report_url) }}" target="_blank">
                                            <img src="{{ asset($data->report_url) }}" alt="" height="100px">
                                        </a>
                                    @else
                                        <a href="{{ asset($data->report_url) }}" target="_blank">
                                            {{ $data->report_name }}
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Proposal
                            </label>
                            <div class="col-sm-9 col-form-label">
                                @if ($data->proposal_url)
                                    @if (explode('.', $data->proposal_url)[1] != 'pdf')
                                        <a href="{{ asset($data->proposal_url) }}" target="_blank">
                                            <img src="{{ asset($data->proposal_url) }}" alt="" height="150px">
                                        </a>
                                    @else
                                        <a href="{{ asset($data->proposal_url) }}" target="_blank">
                                            {{ $data->proposal_name }}
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                KRS
                            </label>
                            <div class="col-sm-9 col-form-label">
                                @if ($data->krs_url)
                                    @if (explode('.', $data->krs_url)[1] != 'pdf')
                                        <a href="{{ asset($data->krs_url) }}" target="_blank">
                                            <img src="{{ asset($data->krs_url) }}" alt="" height="150px">
                                        </a>
                                    @else
                                        <a href="{{ asset($data->krs_url) }}" target="_blank">
                                            {{ $data->krs_name }}
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Transkrip Nilai
                            </label>
                            <div class="col-sm-9 col-form-label">
                                @if ($data->transcript_url)
                                    @if (explode('.', $data->transcript_url)[1] != 'pdf')
                                        <a href="{{ asset($data->transcript_url) }}" target="_blank">
                                            <img src="{{ asset($data->transcript_url) }}" alt="" height="100px">
                                        </a>
                                    @else
                                        <a href="{{ asset($data->transcript_url) }}" target="_blank">
                                            {{ $data->transcript_name }}
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-info px-3">Submit</button>
                        <a href="{{ route('finalproject-seminar.list') }}" class="btn btn-default float-right px-3">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('/css/main.css') }}">
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");

        $('.select2bs4').select2()
        $('[data-mask]').inputmask()

        $('.custom-file-input').on('change', function(e) {
            let fileName = e.target.files[0].name;
            let label = e.target.nextElementSibling;
            label.textContent = fileName;
        });
    </script>
@stop
