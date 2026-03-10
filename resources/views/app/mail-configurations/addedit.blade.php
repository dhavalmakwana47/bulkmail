@extends('app.layout.app')
@section('page_title')
    {{ isset($mailConfiguration) ? 'Edit' : 'Add' }} Mail Configuration
@endsection
@section('header-script')
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .error {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($mailConfiguration) ? 'Edit' : 'Add' }} Mail Configuration</h3>
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                </div>
                <form id="mailConfigForm"
                    action="{{ isset($mailConfiguration) ? route('mail-configurations.update', $mailConfiguration->id) : route('mail-configurations.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($mailConfiguration))
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        @if (auth()->user()->type != '1')
                            <div class="form-group">
                                <label>Corporate Debtor</label>
                                <select name="user_id" id="debtorSelect" class="form-control" required>
                                    <option value="">Select Corporate Debtor</option>
                                    @foreach ($corporateDebtors as $debtor)
                                        <option value="{{ $debtor->id }}" data-name="{{ $debtor->name }}"
                                            data-email="{{ $debtor->email }}"
                                            {{ old('user_id', $mailConfiguration->user_id ?? '') == $debtor->id ? 'selected' : '' }}>
                                            {{ $debtor->name }} ({{ $debtor->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <div id="contactsList" class="mt-2" style="display: none;">
                                    <small class="text-muted">Contacts: <span id="contactCount" class="badge badge-info"
                                            style="cursor: pointer;">0</span></small>
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="user_id" id="debtorSelect" value="{{ auth()->id() }}">
                        @endif
                        <div class="form-group">
                            <label>From Name</label>
                            <input type="text" name="from_name" id="fromName" class="form-control"
                                value="{{ old('from_name', $mailConfiguration->from_name ?? '') }}" required readonly>
                        </div>
                        <div class="form-group">
                            <label>Reply Email</label>
                            <input type="email" name="reply_email" id="replyEmail" class="form-control"
                                value="{{ old('reply_email', $mailConfiguration->reply_email ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="subject" class="form-control"
                                value="{{ old('subject', $mailConfiguration->subject ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Body</label>
                            <small class="text-muted d-block mb-2">
                                Available tags: @{{ name }}, @{{ email }}, @{{ phone }},
                                @{{ attribute_1 }}, @{{ attribute_2 }}, @{{ attribute_3 }},
                                @{{ attribute_4 }}, @{{ attachment_list }}
                            </small>
                            <textarea name="body" id="bodyEditor" class="form-control" rows="5" required>{{ old('body', $mailConfiguration->body ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Attachments</label>
                            <select name="attachments[]" id="attachmentsSelect" class="form-control" multiple>
                            </select>
                            <small class="text-muted">Select multiple attachments. Use @{{ attachment_list }} tag in body to
                                display selected attachments as table.</small>
                        </div>
                        <div class="form-group">
                            <label>Send Type</label>
                            <select name="send_type" id="sendType" class="form-control" required>
                                <option value="NOW"
                                    {{ old('send_type', $mailConfiguration->send_type ?? '') == 'NOW' ? 'selected' : '' }}>
                                    NOW</option>
                                <option value="SCHEDULED"
                                    {{ old('send_type', $mailConfiguration->send_type ?? '') == 'SCHEDULED' ? 'selected' : '' }}>
                                    SCHEDULED</option>
                            </select>
                        </div>
                        <div class="form-group" id="scheduledAtGroup" style="display: none;">
                            <label>Scheduled At</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduledAt" class="form-control"
                                value="{{ old('scheduled_at', isset($mailConfiguration) && $mailConfiguration->scheduled_at ? Carbon\Carbon::parse($mailConfiguration->scheduled_at)->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('mail-configurations.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('customdownload/js/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
        var contactsData = [];
        var attachmentsData = [];

        // Form Validation
        $('#mailConfigForm').validate({
            ignore: '',
            rules: {
                user_id: {
                    required: true
                },
                from_name: {
                    required: true
                },
                reply_email: {
                    required: true,
                    email: true
                },
                subject: {
                    required: true
                },
                body: {
                    required: function() {
                        return $('#bodyEditor').summernote('isEmpty');
                    }
                },
                send_type: {
                    required: true
                },
                scheduled_at: {
                    required: function() {
                        return $('#sendType').val() === 'SCHEDULED';
                    }
                }
            },
            messages: {
                user_id: 'Please select a corporate debtor',
                from_name: 'Please enter from name',
                reply_email: {
                    required: 'Please enter reply email',
                    email: 'Please enter a valid email address'
                },
                subject: 'Please enter subject',
                body: 'Please enter email body',
                send_type: 'Please select send type',
                scheduled_at: 'Please select scheduled date and time'
            },
            errorPlacement: function(error, element) {
                if (element.attr('name') === 'body') {
                    error.insertAfter('.note-editor');
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                $('#bodyEditor').val($('#bodyEditor').summernote('code'));

                var selectedAttachments = $('#attachmentsSelect').val();
                var bodyContent = $('#bodyEditor').summernote('code');

                if (selectedAttachments && selectedAttachments.length > 0) {
                    if (bodyContent.indexOf('@{{ attachment_list }}') === -1) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Tag',
                            text: 'You have selected attachments but the @{{ attachment_list }} tag is missing in the email body. Attachments will not be displayed to recipients.',
                            showCancelButton: true,
                            confirmButtonText: 'Continue Anyway',
                            cancelButtonText: 'Go Back'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                        return false;
                    }
                }

                form.submit();
            }
        });

        // Initialize Select2
        $('#attachmentsSelect').select2({
            placeholder: 'Select attachments',
            allowClear: true
        });

        // Initialize Summernote
        $('#bodyEditor').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']],
                ['mybutton', ['insertTag']]
            ],
            callbacks: {
                onChange: function(contents) {
                    $('#bodyEditor').val(contents);
                    if (!$('#bodyEditor').summernote('isEmpty')) {
                        $('#bodyEditor').valid();
                    }
                }
            },
            buttons: {
                insertTag: function(context) {
                    var ui = $.summernote.ui;
                    var button = ui.buttonGroup([
                        ui.button({
                            contents: '<i class="fa fa-tag"/> Insert Tag',
                            click: function() {
                                var tags = [{
                                        value: '@{{ name }}',
                                        text: 'Name'
                                    },
                                    {
                                        value: '@{{ email }}',
                                        text: 'Email'
                                    },
                                    {
                                        value: '@{{ phone }}',
                                        text: 'Phone'
                                    },
                                    {
                                        value: '@{{ attribute_1 }}',
                                        text: 'Attribute 1'
                                    },
                                    {
                                        value: '@{{ attribute_2 }}',
                                        text: 'Attribute 2'
                                    },
                                    {
                                        value: '@{{ attribute_3 }}',
                                        text: 'Attribute 3'
                                    },
                                    {
                                        value: '@{{ attribute_4 }}',
                                        text: 'Attribute 4'
                                    },
                                    {
                                        value: '@{{ attachment_list }}',
                                        text: 'Attachment List (Table)'
                                    }
                                ];
                                var html = '<select id="tagSelect" class="form-control">';
                                html += '<option value="">Select Tag</option>';
                                tags.forEach(function(tag) {
                                    html += '<option value="' + tag.value + '">' + tag
                                        .text + '</option>';
                                });
                                html += '</select>';

                                Swal.fire({
                                    title: 'Insert Contact Tag',
                                    html: html,
                                    showCancelButton: true,
                                    confirmButtonText: 'Insert',
                                    preConfirm: () => {
                                        return $('#tagSelect').val();
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed && result.value) {
                                        $('#bodyEditor').summernote('insertText', result
                                            .value);
                                    }
                                });
                            }
                        })
                    ]);
                    return button.render();
                }
            }
        });

        $('#debtorSelect').change(function() {
            var selectedOption = $(this).find('option:selected');
            var debtorName = selectedOption.data('name') || '{{ auth()->user()->name }}';
            var debtorEmail = selectedOption.data('email') || '{{ auth()->user()->email }}';
            var userId = $(this).val();
            $('#fromName').val(debtorName || '');
            $('#replyEmail').val(debtorEmail || '');

            if (userId) {
                $.ajax({
                    url: "{{ route('contacts.by-debtor') }}",
                    type: 'GET',
                    data: {
                        user_id: userId
                    },
                    success: function(response) {
                        contactsData = response.contacts;
                        $('#contactCount').text(response.count);
                        $('#contactsList').show();
                    }
                });

                // Fetch attachments
                $.ajax({
                    url: "{{ route('debtor-attachments.by-debtor') }}",
                    type: 'GET',
                    data: {
                        user_id: userId
                    },
                    success: function(response) {
                        attachmentsData = response;
                        $('#attachmentsSelect').empty();
                        response.forEach(function(att) {
                            var selected = @json(isset($mailConfiguration)
                                    ? $mailConfiguration->configurationAttachments->pluck('debtor_attachment_id')->toArray()
                                    : []
                            );
                            var isSelected = selected.includes(att.id);
                            var option = new Option(att.name, att.id, isSelected, isSelected);
                            $('#attachmentsSelect').append(option);
                        });
                        $('#attachmentsSelect').trigger('change');
                    }
                });
            } else {
                $('#contactsList').hide();
                $('#attachmentsSelect').empty();
                contactsData = [];
                attachmentsData = [];
            }
        });

        $('#contactCount').click(function() {
            var userId = $('#debtorSelect').val();
            if (!userId) return;

            var html = '<table id="contactsPopupTable" class="table table-bordered" style="width:100%">' +
                '<thead><tr><th>Name</th><th>Email</th></tr></thead></table>';

            Swal.fire({
                title: 'Contacts List',
                html: html,
                width: '700px',
                showCloseButton: true,
                showConfirmButton: false,
                didOpen: () => {
                    $('#contactsPopupTable').DataTable({
                        processing: true,
                        serverSide: true,
                        pageLength: 10,
                        ajax: {
                            url: "{{ route('contacts.index') }}",
                            data: {
                                debtor_id: userId
                            }
                        },
                        columns: [{
                                data: 'name',
                                name: 'contacts.name'
                            },
                            {
                                data: 'email',
                                name: 'contacts.email'
                            }
                        ]
                    });
                },
                willClose: () => {
                    if ($.fn.DataTable.isDataTable('#contactsPopupTable')) {
                        $('#contactsPopupTable').DataTable().destroy();
                    }
                }
            });
        });

        $(document).ready(function() {
            if ($('#debtorSelect').val()) {
                $('#debtorSelect').trigger('change');
            }

            // Toggle scheduled at field
            function toggleScheduledAt() {
                if ($('#sendType').val() === 'SCHEDULED') {
                    $('#scheduledAtGroup').show();
                } else {
                    $('#scheduledAtGroup').hide();
                    $('#scheduledAt').val('');
                }
            }

            toggleScheduledAt();
            $('#sendType').change(toggleScheduledAt);
        });
    </script>
@endsection
