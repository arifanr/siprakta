@extends('adminlte::page')

@section('title', 'Tugas Akhir')

@section('content_header')
    <h1><b>Tugas Akhir</b></h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card card-info">
        <div class="card-header px-4 border-bottom-none">
            <div class="card-tools">
                @can('CreateFinalProject')
                    @if (Auth::user()->hasRole('admin'))
                        <a href="{{ route('finalproject.create') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-fw fa-plus"></i>
                            Ajukan Tugas Akhir
                        </a>
                    @elseif (count($data) < 1)
                        <a href="{{ route('finalproject.create') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-fw fa-plus"></i>
                            Ajukan Tugas Akhir
                        </a>
                    @endif
                @endcan
            </div>
        </div>
        <div class="card-body pt-1 px-3">
            @can('ApproveDenyFinalProject')
                <form action="{{ route('finalproject.list') }}" class="card-tools float-right">
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <input type="text" name="keyword" class="form-control float-right" placeholder="Search">

                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            @endcan
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        @if (!Auth::user()->hasRole('student'))
                            <th>NPM</th>
                            <th>Nama</th>
                        @endif
                        <th>Judul</th>
                        <th>Pembimbing 1</th>
                        <th>Pembimbing 2</th>
                        <th class="text-center">Status</th>
                        @if (Auth::user()->hasRole('student'))
                            <th class="text-center" style="width: 165px">Action</th>
                        @else
                            <th class="text-center" style="width: 300px">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if (count($data) > 0)
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                @if (!Auth::user()->hasRole('student'))
                                    <td>{{ $item->username }}</td>
                                    <td>{{ $item->name }}</td>
                                @endif
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->supervisor_1 }}</td>
                                <td>{{ $item->supervisor_2 }}</td>
                                <td class="text-center">
                                    @if ($item->status == 0)
                                        <span class="badge bg-info">Submited</span>
                                    @elseif ($item->status == 1)
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Denied</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @can('ApproveDenyFinalProject')
                                        @if ($item->status == 0)
                                            <form action="{{ route('finalproject.approve', [$item->id]) }}" method="post"
                                                class="d-inline-block">
                                                @csrf
                                                {{ method_field('patch') }}
                                                <button type="submit" class="btn btn-success btn-xs">
                                                    <i class="fas fa-fw fa-thumbs-up"></i>
                                                    Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-xs btn-deny" data-toggle="modal"
                                                data-id="{{ $item->id }}" data-target="#modal-default">
                                                <i class="fas fa-fw fa-thumbs-down"></i>
                                                Deny
                                            </button>
                                        @endif
                                    @endcan
                                    @can('EditFinalProject')
                                        @if ($item->status != 1)
                                            <a href="{{ route('finalproject.edit', [$item->id]) }}"
                                                class="btn btn-warning btn-xs px-3">
                                                <i class="fas fa-fw fa-pencil-alt"></i>
                                                Edit
                                            </a>
                                        @endif
                                    @endcan
                                    <a href="{{ route('finalproject.detail', [$item->id]) }}"
                                        class="btn btn-info btn-xs px-3">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center">
                                <i>Not data found</i>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if (count($data) > 0)
            <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                    <li class="page-item">{{ $data->links() }}</li>
                </ul>
            </div>
        @endif
        {{-- <div class="card-footer clearfix">
            <ul class="pagination pagination-sm m-0 float-right">
                <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
            </ul>
        </div> --}}
    </div>

    <div class="modal fade" id="modal-deny">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('finalproject.deny', ['id' => ':id']) }}" method="post">
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
    <script>
        $(document).ready(function() {
            $('.btn-deny').click(function() {
                var finalprojectId = $(this).data('id');
                $('#finalproject-id').val(finalprojectId);

                var formAction = '{{ route('finalproject.deny', ['id' => ':id']) }}';
                formAction = formAction.replace(':id', finalprojectId);
                $('#modal-deny form').attr('action', formAction);

                $('#modal-deny').modal('show');
            });
        });
    </script>
@stop
