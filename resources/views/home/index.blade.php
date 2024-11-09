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
