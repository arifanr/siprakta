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
                        <a href="{{ route('internship-seminar.list') }}" class="btn btn-dark btn-sm">
                            Back
                        </a>
                    </div>
                </div>
                <form class="form-horizontal" action="{{ route('internship-seminar.update', $id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    {{ method_field('patch') }}
                    <input type="hidden" name="transcript_id" value="{{ $data->transcript_id }}">
                    <input type="hidden" name="krs_id" value="{{ $data->krs_id }}">
                    <input type="hidden" name="registration_id" value="{{ $data->registration_id }}">
                    <input type="hidden" name="report_id" value="{{ $data->report_id }}">
                    <input type="hidden" name="assessment_sheet_id" value="{{ $data->assessment_sheet_id }}">
                    <input type="hidden" name="mentor_id" value="{{ $data->mentor_id }}">
                    <input type="hidden" name="examiner1_id" value="{{ $data->examiner1_id }}">
                    <input type="hidden" name="examiner2_id" value="{{ $data->examiner2_id }}">
                    <input type="hidden" name="schedule_old" value="{{ $data->schedule }}">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Pembimbing KP</label>
                            <div class="col-sm-9">
                                <select class="form-control select2bs4 w-full" name="mentor">
                                    <option value="">-- Pembimbing KP --</option>
                                    @foreach ($mentorInternships as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $data->mentor_id == $item->id ? 'selected' : '' }}>{{ $item->name }}
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
                            <label for="" class="col-sm-3 col-form-label">
                                Nama Perusahaan
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="company_name"
                                    value="{{ $data->company_name }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Alamat Perusahaan
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="company_address"
                                    value="{{ $data->company_address }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                No. Telepon Perusahaan
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="company_phone" pattern="\d*"
                                    oninput="this.value = this.value.slice(0, 13);" value="{{ $data->company_phone }}"
                                    required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Tanggal Mulai
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="start_date" id="datemask1"
                                        data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask
                                        value="{{ \Carbon\Carbon::parse($data->start_date)->timezone(session('timezone', 'Asia/Jakarta'))->format('d/m/Y') }}"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Tanggal Berakhir
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="end_date" id="datemask2"
                                        data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask
                                        value="{{ \Carbon\Carbon::parse($data->end_date)->timezone(session('timezone', 'Asia/Jakarta'))->format('d/m/Y') }}"
                                        required>
                                </div>
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
                                KRS
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group mb-2">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile2"
                                            name="krs" accept=".pdf, .webp, .png, .jpeg, .jpg"
                                            value="{{ old('krs') }}">
                                        <label class="custom-file-label" for="exampleInputFile2">Choose file</label>
                                    </div>
                                </div>
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
                            <div class="col-sm-9">
                                <div class="input-group mb-2">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile3"
                                            name="transcript" accept=".pdf, .webp, .png, .jpeg, .jpg"
                                            value="{{ old('transcript') }}">
                                        <label class="custom-file-label" for="exampleInputFile3">Choose file</label>
                                    </div>
                                </div>
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
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Laporan KP
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
                                Lembar Penilaian KP
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile"
                                            name="assessment_sheet" accept=".pdf, .webp, .png, .jpeg, .jpg">
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                </div>
                                @if ($data->assessment_url)
                                    @if (explode('.', $data->assessment_url)[1] != 'pdf')
                                        <a href="{{ asset($data->assessment_url) }}" target="_blank">
                                            <img src="{{ asset($data->assessment_url) }}" alt="" height="100px">
                                        </a>
                                    @else
                                        <a href="{{ asset($data->assessment_url) }}" target="_blank">
                                            {{ $data->assessment_name }}
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                        @if (!Auth::user()->hasRole('student'))
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">
                                    Jadwal Seminar
                                    <span class="text-red">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="schedule"
                                            data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy HH:MM"
                                            data-mask
                                            value="{{ $data->schedule ? \Carbon\Carbon::parse($data->schedule)->timezone(session('timezone', 'Asia/Jakarta'))->format('d/m/Y H:i') : '' }}"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">Penguji KP 1</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2bs4 w-full" name="examiner1">
                                        <option value="">-- Penguji KP --</option>
                                        @foreach ($examiners as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $data->examiner1_id == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">Penguji KP 2</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2bs4 w-full" name="examiner2">
                                        <option value="">-- Penguji KP --</option>
                                        @foreach ($examiners as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $data->examiner2_id == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-info px-3">Submit</button>
                        <a href="{{ route('internship-seminar.list') }}" class="btn btn-default float-right px-3">
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
