@extends('adminlte::page')

@section('title', 'Home')

@section('content_header')
    <h1><b>Home</b></h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3>Your Information</h3><br>
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td style="width: 120px">NIP</td>
                                <td>: {{ Auth::user()->username }}</td>
                            </tr>
                            <tr>
                                <td>Nama Lengkap</td>
                                <td>: {{ Auth::user()->name }}</td>
                            </tr>
                            <tr>
                                <td>Nama Prodi</td>
                                <td>: Informatika</td>
                            </tr>
                            <tr>
                                <td>Jenjang Prodi</td>
                                <td>: </td>
                            </tr>
                            <tr>
                                <td>Status User</td>
                                <td>:
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
            </div>
        </div>
    </div>

    @can('status')

        <div class="row justify-content-center">
            @if ($internship)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3>Status Kerja Praktik</h3><br>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td style="width: 120px">Tahapan</td>
                                        <td>:
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
                                        <td>:
                                            {{ $internship->schedule
                                                ? \Carbon\Carbon::parse($internship->schedule)->timezone(session('timezone', 'Asia/Jakarta'))->format('d M Y')
                                                : 'Belum ada Jadwal' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nilai</td>
                                        <td>: {{ $internship->grade }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            @endif

            @if ($finalproject)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3>Status Tugas Akhir</h3><br>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td style="width: 120px">Tahapan</td>
                                        <td>:
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
                                        <td>:
                                            {{ $finalproject->schedule
                                                ? \Carbon\Carbon::parse($finalproject->schedule)->timezone(session('timezone', 'Asia/Jakarta'))->format('d M Y')
                                                : 'Belum ada Jadwal' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nilai</td>
                                        <td>: {{ $finalproject->grade }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            @endif
        </div>

    @endcan
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
