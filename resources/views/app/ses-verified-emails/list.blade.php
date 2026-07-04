@extends('app.layout.app')
@section('page_title')
    SES Verified Emails
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
                    <h3 class="card-title" style="font-size: 21px;">SES Verified Emails</h3>
                    <div class="float-right">
                        <a href="{{ route('ses-verified-emails.create') }}" class="btn btn-primary">Add Email</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="ses_verified_emails_list" class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Email</th>
                                    <th>Status</th>
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
        var table = $('#ses_verified_emails_list').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: "{{ route('ses-verified-emails.index') }}",
            order: [[0, "desc"]],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'email', name: 'email' },
                { data: 'active_status', name: 'active_status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        function deleteItem(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this verified email",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('ses-verified-emails.index') }}/" + id,
                        data: { _method: 'DELETE', _token: "{{ csrf_token() }}" },
                        success: function() {
                            table.ajax.reload();
                            Swal.fire('Deleted!', 'Email has been deleted.', 'success');
                        },
                        error: function() {
                            Swal.fire('Error!', 'Failed to delete email.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
