@extends('app.layout.app')
@section('page_title')
    Corporate Debtors
@endsection
@section('header-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 21px;">Corporate Debtors</h3>

                    {{-- Data insert button with check permission --}}
                    <a href="{{ route('corporate-debtors.create') }}" class="btn btn-primary float-right nav-link">Add Corporate Debtor</a>
                </div>

                {{-- Datatable --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users_list" class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script src=" {{ asset('customdownload/js/jquery.dataTables.min.js') }}"></script>
    <script>
        var table = $('#users_list').DataTable({
            processing: true,
            serverSide: true,
            "pageLength": 10,
            ajax: "{{ route('corporate-debtors.index') }}",
            "order": [[0, "desc"]],
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            "fnDrawCallback": function() {
                $("input[data-bootstrap-switch]").each(function() {
                    $(this).bootstrapSwitch();
                })
            }
        });

        function changeUserStatus(id) {
            var token = $("meta[name='csrf-token']").attr("content");

            $.ajax({
                type: 'POST',
                url: "{{ route('corporate-debtors.changestatus') }}",
                data: {
                    id: id,
                    "_token": token,
                },
                success: function(data) {
                    table.ajax.reload(null, false);
                },
                beforeSend: function() {},
            });
        }

        function deleteUser(id) {
            var token = $("meta[name='csrf-token']").attr("content");

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this user",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{  route('corporate-debtors.index') }}"+"/"+ id; 
                    $.ajax({
                        type: 'DELETE',
                        url: url,
                        data: {"_token": "{{ csrf_token() }}"},
                        success: function () {
                            createMessage('User deleted successfully')
                            table.ajax.reload();
                        },
                        beforeSend: function () {
                        },
                    });
                }
            })
        }
    </script>
@endsection
