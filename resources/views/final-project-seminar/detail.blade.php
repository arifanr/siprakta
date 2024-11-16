@extends('adminlte::page')

@section('title', 'Seminar Kerja Praktik')

@section('content_header')
    <h1><b>Seminar Kerja Praktik</b></h1>
@stop

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card card-info">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Detail Seminar Kerja Praktik</h3>
                    <div class="card-tools text-right">
                        @can('ApproveDenySeminar')
                            @if ($data->status == 0)
                                <form action="{{ route('finalproject-seminar.approve', [$data->id]) }}" method="post"
                                    class="d-inline-block">
                                    @csrf
                                    {{ method_field('patch') }}
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-fw fa-thumbs-up"></i>
                                        Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                    data-target="#modal-deny">
                                    <i class="fas fa-fw fa-thumbs-down"></i>
                                    Deny
                                </button>
                            @elseif ($data->status != 0)
                                <form action="{{ route('finalproject-seminar.reset', [$data->id]) }}" method="post"
                                    class="d-inline-block">
                                    @csrf
                                    {{ method_field('patch') }}
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-fw fa-sync-alt"></i>
                                        Reset Status
                                    </button>
                                </form>
                            @endif
                        @endcan
                        @can('EditSeminarStudent')
                            @if ($data->status != 1)
                                <a href="{{ route('finalproject-seminar.edit', [$data->id]) }}"
                                    class="btn btn-warning btn-sm px-3">
                                    <i class="fas fa-fw fa-pencil-alt"></i>
                                    Edit
                                </a>
                            @endif
                        @endcan
                        @can('EditSeminar')
                            <a href="{{ route('finalproject-seminar.edit', [$data->id]) }}" class="btn btn-warning btn-sm px-3">
                                <i class="fas fa-fw fa-pencil-alt"></i>
                                Edit
                            </a>
                        @endcan
                        <a href="{{ route('finalproject-seminar.list') }}" class="btn btn-dark btn-sm">
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Jadwal Seminar</td>
                                <td style="width: 10px">:</td>
                                <td>
                                    @if ($data->schedule)
                                        {{ \Carbon\Carbon::parse($data->schedule)->timezone(session('timezone', 'Asia/Jakarta'))->format('d M Y H:i') }}
                                        WIB
                                    @else
                                        <i>Menunggu Jadwal</i>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Penguji 1</td>
                                <td>:</td>
                                <td>{{ $data->examiner1 }}</td>
                            </tr>
                            <tr>
                                <td>Penguji 2</td>
                                <td>:</td>
                                <td>{{ $data->examiner2 }}</td>
                            </tr>
                            <tr>
                                <td>Penguji 2</td>
                                <td>:</td>
                                <td>{{ $data->examiner3 }}</td>
                            </tr>
                            <tr>
                                <td style="width: 20%">NPM</td>
                                <td>:</td>
                                <td>{{ $data->username }}</td>
                            </tr>
                            <tr>
                                <td>Nama</td>
                                <td>:</td>
                                <td>{{ $data->name }}</td>
                            </tr>
                            <tr>
                                <td>Pembimbing 1</td>
                                <td>:</td>
                                <td>{{ $data->supervisor_1 }}</td>
                            </tr>
                            <tr>
                                <td>Pembimbing 2</td>
                                <td>:</td>
                                <td>{{ $data->supervisor_2 }}</td>
                            </tr>
                            <tr>
                                <td>Judul</td>
                                <td>:</td>
                                <td>{{ $data->title }}</td>
                            </tr>
                            <tr>
                                <td>Deskripsi</td>
                                <td>:</td>
                                <td>{{ $data->description }}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>:</td>
                                <td>
                                    @if ($data->status == 0)
                                        <span class="badge bg-info">Submited</span>
                                    @elseif ($data->status == 1)
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Denied</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($data->status == 2)
                                <tr>
                                    <td>Alasan Ditolak</td>
                                    <td>:</td>
                                    <td class="text-red">
                                        {{ $data->reason }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td>Formulir Pendaftaran</td>
                                <td>:</td>
                                <td>
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
                                </td>
                            </tr>
                            <tr>
                                <td>Laporan TA</td>
                                <td>:</td>
                                <td>
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
                                </td>
                            </tr>
                            <tr>
                                <td>Proposal</td>
                                <td>:</td>
                                <td>
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
                                </td>
                            </tr>
                            <tr>
                                <td>KRS</td>
                                <td>:</td>
                                <td>
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
                                </td>
                            </tr>
                            <tr>
                                <td>Transkrip Nilai</td>
                                <td>:</td>
                                <td>
                                    @if ($data->transcript_url)
                                        @if (explode('.', $data->transcript_url)[1] != 'pdf')
                                            <a href="{{ asset($data->transcript_url) }}" target="_blank">
                                                <img src="{{ asset($data->transcript_url) }}" alt=""
                                                    height="100px">
                                            </a>
                                        @else
                                            <a href="{{ asset($data->transcript_url) }}" target="_blank">
                                                {{ $data->transcript_name }}
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('internship.list') }}" class="btn btn-default float-right px-3">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-deny">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('finalproject-seminar.deny', ['id' => $data->id]) }}" method="post">
                    @csrf
                    {{ method_field('patch') }}
                    <div class="modal-header">
                        <h4 class="modal-title">Deny</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="" class="col-form-label">Alasan</label>
                            <textarea class="form-control" name="reason" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
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
    <script></script>
@stop
