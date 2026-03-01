@extends('app.layout.app')
@section('page_title')
    Debtor Attachments
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
                    <h3 class="card-title" style="font-size: 21px;">Debtor Attachments</h3>
                    <div class="float-right">
                        <a href="{{ route('debtor-attachments.create') }}" class="btn btn-primary">Add Attachment</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="attachments_list" class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    @if(auth()->user()->type != '1')
                                    <th>Corporate Debtor</th>
                                    @endif
                                    <th>Name</th>
                                    <th>File Name</th>
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
    <script src="{{ asset('customdownload/js/jquery.dataTables.min.js') }}"></script>
    <script>
        var isDebtor = {{ auth()->user()->type == '1' ? 'true' : 'false' }};
        var columns = [{data: 'id', name: 'id'}];
        
        if (!isDebtor) {
            columns.push({data: 'corporate_debtor', name: 'user.name'});
        }
        columns.push(
            {data: 'name', name: 'name'},
            {data: 'file_name', name: 'file_name'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        );
        
        var table = $('#attachments_list').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: "{{ route('debtor-attachments.index') }}",
            order: [[0, "desc"]],
            columns: columns
        });

        function deleteItem(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this attachment",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'DELETE',
                        url: "{{ route('debtor-attachments.index') }}/" + id,
                        data: {_token: "{{ csrf_token() }}"},
                        success: function() {
                            table.ajax.reload();
                        }
                    });
                }
            });
        }
    </script>
@endsection
