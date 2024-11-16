@extends('adminlte::page')

@section('title', 'Home')

@section('content_header')
    <h1 class="mb-3">Home</h1>
@stop

@section('content')
    <div class="{{ !Auth::user()->hasRole('student') ? 'row justify-content-center' : 'row' }}">
        @can('status')
            <div class="col-md-6">
                <div class="row">
                    @if ($internship)
                        <div class="col-md-12">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <b>Status Kerja Praktik</b>
                                    </h3>
                                </div>
                                <div class="card-body table-responsive">
                                    <table class="table table-borderless table-sm">
                                        <tbody>
                                            <tr>
                                                <td style="width: 120px">Status</td>
                                                <td style="width: 10px">:</td>
                                                <td>
                                                    @if ($internship->status == 0)
                                                        Mengajukan KP
                                                    @elseif ($internship->status == 1)
                                                        @if ($internship->seminar_status == 0)
                                                            Mengajukan Seminar KP
                                                        @elseif ($internship->seminar_status == 1)
                                                            Seminar KP Disetujui
                                                        @elseif ($internship->seminar_status == 2)
                                                            <span class="text-red">Seminar KP Ditolak</span>
                                                        @else
                                                            KP Disetujui
                                                        @endif
                                                    @elseif ($internship->status == 2)
                                                        <span class="text-red">KP Ditolak</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Jadwal Seminar</td>
                                                <td>:</td>
                                                <td>
                                                    {{ $internship->schedule
                                                        ? \Carbon\Carbon::parse($internship->schedule)->timezone(session('timezone', 'Asia/Jakarta'))->format('d M Y H:i') . ' WIB'
                                                        : 'Belum ada Jadwal' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Nilai</td>
                                                <td>:</td>
                                                <td>{{ $internship->grade }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer"></div>
                            </div>
                        </div>
                    @endif

                    @if ($finalproject)
                        <div class="col-md-12">
                            <div class="card card-outline card-danger">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <b>Status Tugas Akhir</b>
                                    </h3>
                                </div>
                                <div class="card-body table-responsive">
                                    <table class="table table-borderless table-sm">
                                        <tbody>
                                            <tr>
                                                <td style="width: 120px">Status</td>
                                                <td style="width: 10px">:</td>
                                                <td>
                                                    @if ($finalproject->status == 0)
                                                        Mengajukan TA
                                                    @elseif ($finalproject->status == 1)
                                                        @if ($finalproject->seminar_status == 0)
                                                            Mengajukan Seminar TA
                                                        @elseif ($finalproject->seminar_status == 1)
                                                            Seminar TA Disetujui
                                                        @elseif ($finalproject->seminar_status == 2)
                                                            <span class="text-red">Seminar TA Ditolak</span>
                                                        @else
                                                            TA Disetujui
                                                        @endif
                                                    @elseif ($finalproject->status == 2)
                                                        <span class="text-red">TA Ditolak</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Jadwal Seminar</td>
                                                <td>:</td>
                                                <td>
                                                    {{ $finalproject->schedule
                                                        ? \Carbon\Carbon::parse($finalproject->schedule)->timezone(session('timezone', 'Asia/Jakarta'))->format('d M Y H:i') . ' WIB'
                                                        : 'Belum ada Jadwal' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Nilai</td>
                                                <td>:</td>
                                                <td>{{ $finalproject->grade }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endcan
        <div class="col-md-6">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <b>Your Information</b>
                    </h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-borderless table-sm">
                        <tbody>
                            <tr>
                                <td style="width: 120px">Username</td>
                                <td style="width: 10px">:</td>
                                <td>{{ Auth::user()->username }}</td>
                            </tr>
                            <tr>
                                <td>Nama Lengkap</td>
                                <td>:</td>
                                <td>{{ Auth::user()->name }}</td>
                            </tr>
                            <tr>
                                <td>Nama Prodi</td>
                                <td>:</td>
                                <td>Informatika</td>
                            </tr>
                            <tr>
                                <td>Jenjang Prodi</td>
                                <td>:</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Status User</td>
                                <td>:</td>
                                <td>
                                    @if (Auth::user()->hasRole('admin'))
                                        Admin
                                    @endif
                                    @if (Auth::user()->hasRole('student'))
                                        Mahasiswa
                                    @endif
                                    @if (Auth::user()->hasRole('koordinator'))
                                        Koordinator
                                    @endif
                                    @if (Auth::user()->hasRole('pembimbing_kp'))
                                        Pembimbing KP
                                    @endif
                                    @if (Auth::user()->hasRole('pembimbing_ta'))
                                        Pembimbing Tugas Akhir
                                    @endif
                                    @if (Auth::user()->hasRole('pembimbing_akademik'))
                                        Pembimbing Akademik
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer"></div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        body {
            font-size: 0.9rem;
        }
    </style>
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
