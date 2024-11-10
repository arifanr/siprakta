@extends('adminlte::page')

@section('title', 'Seminar Tugas Akhir')

@section('content_header')
    <h1><b>Seminar Tugas Akhir</b></h1>
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
                    <h3 class="card-title mb-0">Ajukan Seminar Tugas Akhir</h3>
                    <div class="card-tools text-right">
                        <a href="{{ route('finalproject-seminar.list') }}" class="btn btn-dark btn-sm">
                            Back
                        </a>
                    </div>
                </div>
                <form class="form-horizontal" action="{{ route('finalproject-seminar.save') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Pembimbing 1
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control select2bs4 w-full" name="mentor_1" required>
                                    <option value="">-- Pembimbing TA --</option>
                                    @foreach ($mentors as $item)
                                        <option value="{{ $item->id }}" {{ old('mentor_1') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Pembimbing 2
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control select2bs4 w-full" name="mentor_2" required>
                                    <option value="">-- Pembimbing TA --</option>
                                    @foreach ($mentors as $item)
                                        <option value="{{ $item->id }}" {{ old('mentor_2') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
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
                                <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Deskripsi</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="description" rows="5">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Formulir Pendaftaran
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile"
                                            name="registration" accept=".pdf, .webp, .png, .jpeg, .jpg" required>
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                KRS
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile2"
                                            name="krs" accept=".pdf, .webp, .png, .jpeg, .jpg" required>
                                        <label class="custom-file-label" for="exampleInputFile2">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Transkrip Nilai
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile3"
                                            name="transcript" accept=".pdf, .webp, .png, .jpeg, .jpg" value="{{ old('transcript') }}" required>
                                        <label class="custom-file-label" for="exampleInputFile3">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">
                                Laporan TA
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile"
                                            name="report" accept=".pdf, .webp, .png, .jpeg, .jpg" required>
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                </div>
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
                                            data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy HH:MM" data-mask
                                            value="{{ old('end_date') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">Penguji TA 1</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2bs4 w-full" name="examiner1">
                                        <option value="">-- Penguji TA --</option>
                                        @foreach ($examiners as $item)
                                            <option value="{{ $item->id }}" {{ old('examiner1') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">Penguji TA 2</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2bs4 w-full" name="examiner2">
                                        <option value="">-- Penguji TA --</option>
                                        @foreach ($examiners as $item)
                                            <option value="{{ $item->id }}" {{ old('examiner2') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-3 col-form-label">Penguji TA 3</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2bs4 w-full" name="examiner3">
                                        <option value="">-- Penguji TA --</option>
                                        @foreach ($examiners as $item)
                                            <option value="{{ $item->id }}" {{ old('examiner3') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
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
