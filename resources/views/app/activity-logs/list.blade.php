@extends('app.layout.app')
@section('page_title')
    Activity Logs
@endsection
@section('header-script')
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Logs</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <select id="moduleFilter" class="form-control">
                                <option value="">All Modules</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module }}">{{ $module }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="actionFilter" class="form-control">
                                <option value="">All Actions</option>
                                <option value="create">Create</option>
                                <option value="update">Update</option>
                                <option value="delete">Delete</option>
                                <option value="view">View</option>
                                <option value="login">Login</option>
                                <option value="status_change">Status Change</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="userFilter" class="form-control">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" id="dateFrom" class="form-control" placeholder="From Date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" id="dateTo" class="form-control" placeholder="To Date">
                        </div>
                        <div class="col-md-1">
                            <button id="filterBtn" class="btn btn-primary btn-block">Filter</button>
                        </div>
                        <div class="col-md-1">
                            <button id="exportBtn" class="btn btn-success btn-block">Export</button>
                        </div>
                    </div>
                    <table id="activity_logs" class="table table-bordered yajra-datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Module</th>
                                <th>Action</th>
                                <th>User</th>
                                <th>Model Type</th>
                                <th>Model ID</th>
                                <th>IP Address</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script src="{{ asset('customdownload/js/jquery.dataTables.min.js') }}"></script>
    <script>
        var table = $('#activity_logs').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax: {
                url: "{{ route('activity-logs.index') }}",
                data: function(d) {
                    d.module = $('#moduleFilter').val();
                    d.action = $('#actionFilter').val();
                    d.user_id = $('#userFilter').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                }
            },
            order: [[7, "desc"]],
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'module', name: 'module'},
                {data: 'action', name: 'action'},
                {data: 'user_name', name: 'user.name'},
                {data: 'loggable_type', name: 'loggable_type'},
                {data: 'loggable_id', name: 'loggable_id'},
                {data: 'ip_address', name: 'ip_address'},
                {data: 'created_at', name: 'created_at'}
            ]
        });

        $('#filterBtn').click(function() {
            table.ajax.reload();
        });

        $('#exportBtn').click(function() {
            var params = {
                module: $('#moduleFilter').val(),
                action: $('#actionFilter').val(),
                user_id: $('#userFilter').val(),
                date_from: $('#dateFrom').val(),
                date_to: $('#dateTo').val(),
                export: 'csv'
            };
            
            var queryString = $.param(params);
            window.open('{{ route("activity-logs.export") }}?' + queryString, '_blank');
        });
    </script>
@endsection
