@extends('app.layout.app')
@section('page_title')
    Mail Configurations
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
                    <h3 class="card-title" style="font-size: 21px;">Mail Configurations</h3>
                    <div class="float-right">
                        <button type="button" class="btn btn-danger" id="bulkDeleteBtn" style="display:none;">Delete Selected</button>
                        <a href="{{ route('mail-configurations.create') }}" class="btn btn-primary">Add Mail Configuration</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="mails_list" class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>#</th>
                                    @if(auth()->user()->type != '1')
                                    <th>Corporate Debtor</th>
                                    @endif
                                    <th>From Name</th>
                                    <th>Subject</th>
                                    <th>Send Type</th>
                                    <th>Scheduled At</th>
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
        var columns = [{data: 'checkbox', name: 'checkbox', orderable: false, searchable: false}, {data: 'id', name: 'mail_configurations.id'}];
        
        if (!isDebtor) {
            columns.push({data: 'corporate_debtor', name: 'user.name'});
        }
        columns.push(
            {data: 'from_name', name: 'mail_configurations.from_name'},
            {data: 'subject', name: 'mail_configurations.subject'},
            {data: 'send_type', name: 'mail_configurations.send_type'},
            {data: 'scheduled_at', name: 'mail_configurations.scheduled_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        );
        
        var table = $('#mails_list').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: "{{ route('mail-configurations.index') }}",
            order: [[1, "desc"]],
            columns: columns
        });

        $('#selectAll').click(function() {
            $('.item-checkbox').prop('checked', this.checked);
            toggleBulkDeleteBtn();
        });

        $(document).on('change', '.item-checkbox', function() {
            toggleBulkDeleteBtn();
        });

        $('#bulkDeleteBtn').click(function() {
            if ($('.item-checkbox:checked').length === 0) return;
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete selected items",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var ids = [];
                    $('.item-checkbox:checked').each(function() {
                        ids.push($(this).val());
                    });
                    
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('mail-configurations.bulk-delete') }}",
                        data: {ids: ids, _token: "{{ csrf_token() }}"},
                        success: function() {
                            table.ajax.reload();
                            $('#selectAll').prop('checked', false);
                            toggleBulkDeleteBtn();
                        }
                    });
                }
            });
        });

        function toggleBulkDeleteBtn() {
            $('#bulkDeleteBtn').toggle($('.item-checkbox:checked').length > 0);
        }

        function deleteItem(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this item",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'DELETE',
                        url: "{{ route('mail-configurations.index') }}/" + id,
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
