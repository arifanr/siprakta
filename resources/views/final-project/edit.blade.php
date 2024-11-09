@extends('adminlte::page')

@section('title', 'Dashboard')

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
                    <h3 class="card-title mb-0">Edit Tugas Akhir</h3>
                    <div class="card-tools text-right">
                        <a href="{{ route('finalproject.list') }}" class="btn btn-dark btn-sm">
                            Back
                        </a>
                    </div>
                </div>
                <form class="form-horizontal" action="{{ route('finalproject.update', $id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    {{ method_field('patch') }}
                    <input type="hidden" name="transcript_id" value="{{ $data->transcript_id }}">
                    <input type="hidden" name="krs_id" value="{{ $data->krs_id }}">
                    <input type="hidden" name="proposal_id" value="{{ $data->proposal_id }}">
                    <input type="hidden" name="mentor_id" value="{{ $data->mentor_id }}">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Pembimbing TA</label>
                            <div class="col-sm-9">
                                <select class="form-control select2bs4 w-full" name="mentor">
                                    <option value="">-- Pembimbing TA --</option>
                                    @foreach ($mentors as $item)
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
                            <label for="" class="col-sm-3 col-form-label">Proposal/Laporan</label>
                            <div class="col-sm-9">
                                <div class="input-group mb-2">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile"
                                            name="proposal" accept=".pdf, .webp, .png, .jpeg, .jpg"
                                            value="{{ old('proposal') }}">
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                </div>
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
                            <div class="col-sm-9">
                                <div class="input-group mb-2">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile2"
                                            name="krs" accept=".pdf, .webp, .png, .jpeg, .jpg"
                                            value="{{ old('krs') }}" >
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
                                            value="{{ old('transcript') }}" >
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
