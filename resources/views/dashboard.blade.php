@extends('adminlte::page')

@section('title', 'Dashboard')

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
                                <td>NIP</td>
                                <td>: 199408282024061001</td>
                            </tr>
                            <tr>
                                <td>NIDN</td>
                                <td>: </td>
                            </tr>
                            <tr>
                                <td>Nama Lengkap</td>
                                <td>: Arifan Rahman</td>
                            </tr>
                            <tr>
                                <td>Nama Prodi</td>
                                <td>: Informatika</td>
                            </tr>
                            <tr>
                                <td>Jenjang Prodi</td>
                                <td>: S1</td>
                            </tr>
                            <tr>
                                <td>Status User</td>
                                <td>: dosen</td>
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
        body { font-size: 0.9rem; }
    </style>
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
