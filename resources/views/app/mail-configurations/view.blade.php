@extends('app.layout.app')
@section('page_title')
    Mail Configuration Details
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 21px;">Mail Configuration Details</h3>
                    <div class="float-right">
                        <a href="{{ route('mail-configurations.index') }}" class="btn btn-secondary">Back</a>
                        @if($mailConfiguration->status == 0)
                            <a href="{{ route('mail-configurations.edit', $mailConfiguration->id) }}" class="btn btn-info">Edit</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID</th>
                                    <td>{{ $mailConfiguration->id }}</td>
                                </tr>
                                @if(auth()->user()->type != '1')
                                <tr>
                                    <th>Corporate Debtor</th>
                                    <td>{{ $mailConfiguration->user->name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>From Name</th>
                                    <td>{{ $mailConfiguration->from_name }}</td>
                                </tr>
                                <tr>
                                    <th>Reply Email</th>
                                    <td>{{ $mailConfiguration->reply_email }}</td>
                                </tr>
                                <tr>
                                    <th>Subject</th>
                                    <td>{{ $mailConfiguration->subject }}</td>
                                </tr>
                                <tr>
                                    <th>Send Type</th>
                                    <td>{{ is_string($mailConfiguration->send_type) ? $mailConfiguration->send_type : $mailConfiguration->send_type->value }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Status</th>
                                    <td>
                                        @if($mailConfiguration->status == 0)
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($mailConfiguration->status == 1)
                                            <span class="badge badge-info">Processing</span>
                                        @else
                                            <span class="badge badge-success">Completed</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Scheduled At</th>
                                    <td>{{ $mailConfiguration->scheduled_at ? $mailConfiguration->scheduled_at->format('d-m-Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $mailConfiguration->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $mailConfiguration->updated_at->format('d-m-Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Attachments</th>
                                    <td>
                                        @if($mailConfiguration->configurationAttachments->count() > 0)
                                            @foreach($mailConfiguration->configurationAttachments as $attachment)
                                                <div>{{ $attachment->debtorAttachment->name }}</div>
                                            @endforeach
                                        @else
                                            No attachments
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Email Body</h5>
                            <div class="border p-3" style="background: #f9f9f9;">
                                {!! $mailConfiguration->body !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
