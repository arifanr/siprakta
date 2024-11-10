@extends('adminlte::page')

@section('title', 'Tugas Akhir')

@section('content_header')
    <h1><b>Tugas Akhir</b></h1>
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
                    <h3 class="card-title mb-0">Ajukan Tugas Akhir</h3>
                    <div class="card-tools text-right">
                        <a href="{{ route('finalproject.list') }}" class="btn btn-dark btn-sm">
                            Back
                        </a>
                    </div>
                </div>
                <form class="form-horizontal" action="{{ route('finalproject.save') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Pembimbing 1</label>
                            <div class="col-sm-9">
                                <select class="form-control select2bs4 w-full" name="mentor_1">
                                    <option value="">-- Pembimbing TA --</option>
                                    @foreach ($mentors as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('mentor_1') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Pembimbing 2</label>
                            <div class="col-sm-9">
                                <select class="form-control select2bs4 w-full" name="mentor_2">
                                    <option value="">-- Pembimbing TA --</option>
                                    @foreach ($mentors as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('mentor_2') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
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
                                <input type="text" class="form-control" name="title" value="{{ old('title') }}"
                                    required>
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
                                Proposal/Laporan
                                <span class="text-red">*</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile"
                                            name="proposal" accept=".pdf, .webp, .png, .jpeg, .jpg"
                                            value="{{ old('proposal') }}" required>
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                </div>
                                @error('proposal')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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
                                            name="krs" accept=".pdf, .webp, .png, .jpeg, .jpg"
                                            value="{{ old('krs') }}" required>
                                        <label class="custom-file-label" for="exampleInputFile2">Choose file</label>
                                    </div>
                                </div>
                                @error('krs')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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
                                            name="transcript" accept=".pdf, .webp, .png, .jpeg, .jpg"
                                            value="{{ old('transcript') }}" required>
                                        <label class="custom-file-label" for="exampleInputFile3">Choose file</label>
                                    </div>
                                </div>
                                @error('transcript')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-info px-3">Submit</button>
                        <a href="{{ route('finalproject.list') }}" class="btn btn-default float-right px-3">
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
