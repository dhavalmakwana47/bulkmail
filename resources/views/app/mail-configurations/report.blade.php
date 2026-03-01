@extends('app.layout.app')
@section('page_title')
    Mail Report
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
                    <h3 class="card-title">Mail Configuration Report</h3>
                    <div class="float-right">
                        <a href="{{ route('mail-configurations.index') }}" class="btn btn-default">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-envelope"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Contacts</span>
                                    <span class="info-box-number">{{ $stats['total'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sent Successfully</span>
                                    <span class="info-box-number">{{ $stats['sent'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Failed</span>
                                    <span class="info-box-number">{{ $stats['failed'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Success Rate</span>
                                    <span class="info-box-number">{{ $stats['total'] > 0 ? round(($stats['sent'] / $stats['total']) * 100, 2) : 0 }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5>Configuration Details</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Corporate Debtor</th>
                            <td>{{ $mailConfiguration->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>{{ $mailConfiguration->subject }}</td>
                        </tr>
                        <tr>
                            <th>Send Type</th>
                            <td>{{ is_string($mailConfiguration->send_type) ? $mailConfiguration->send_type : $mailConfiguration->send_type->value }}</td>
                        </tr>
                        <tr>
                            <th>Scheduled At</th>
                            <td>{{ $mailConfiguration->scheduled_at ? (is_string($mailConfiguration->scheduled_at) ? $mailConfiguration->scheduled_at : $mailConfiguration->scheduled_at->format('d-m-Y H:i')) : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($mailConfiguration->status == 0)
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($mailConfiguration->status == 1)
                            
                                    <span class="badge badge-success">Completed</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <h5 class="mt-4">Recipient Details</h5>
                    <div class="table-responsive">
                        <table id="recipient_logs" class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Contact Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Sent At</th>
                                    <th>Delivered At</th>
                                    <th>Message ID</th>
                                    <th>Error Message</th>
                                    <th>Bounce Reason</th>
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
        $('#recipient_logs').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: "{{ route('mail-configurations.report', $mailConfiguration->id) }}",
            order: [[0, "asc"]],
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'contact_name', name: 'contact.name'},
                {data: 'contact_email', name: 'contact.email'},
                {data: 'status', name: 'status'},
                {data: 'sent_at', name: 'sent_at'},
                {data: 'delivered_at', name: 'delivered_at'},
                {data: 'message_id', name: 'message_id'},
                {data: 'error_message', name: 'error_message'},
                {data: 'bounce_reason', name: 'bounce_reason'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        function resendMail(logId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to resend this email",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, resend it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('mail-configurations.resend') }}",
                        data: {
                            log_id: logId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire('Success!', response.message, 'success');
                            $('#recipient_logs').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON.message, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
