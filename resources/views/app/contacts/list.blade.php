@extends('app.layout.app')
@section('page_title')
    Contacts
@endsection
@section('header-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
@endsection
@section('content-body')
    @if(session('skipped'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Warning!</strong> The following records were skipped due to duplicate emails:
            <ul class="mt-2 mb-0">
                @foreach(session('skipped') as $skip)
                    <li>{{ $skip['name'] }} ({{ $skip['email'] }}) - {{ $skip['reason'] }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 21px;">Contacts</h3>
                    <div class="float-right">
                        <button type="button" class="btn btn-danger" id="bulkDeleteBtn" style="display:none;">Delete Selected</button>
                        <a href="{{ route('contacts.import') }}" class="btn btn-success">Import Contacts</a>
                        <a href="{{ route('contacts.create') }}" class="btn btn-primary">Add Contact</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        @if(auth()->user()->type != '1')
                        <div class="col-md-3">
                            <label>Filter by Corporate Debtor</label>
                            <select id="debtorFilter" class="form-control">
                                <option value="">All Corporate Debtors</option>
                                @foreach($corporateDebtors as $debtor)
                                    <option value="{{ $debtor->id }}">{{ $debtor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table id="contacts_list" class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>#</th>
                                    @if(auth()->user()->type != '1')
                                    <th>Corporate Debtor</th>
                                    @endif
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Attribute 1</th>
                                    <th>Attribute 2</th>
                                    <th>Attribute 3</th>
                                    <th>Attribute 4</th>
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
        var columns = [{data: 'checkbox', name: 'checkbox', orderable: false, searchable: false}, {data: 'id', name: 'contacts.id'}];
        
        if (!isDebtor) {
            columns.push({data: 'corporate_debtor', name: 'user.name'});
        }
        columns.push(
            {data: 'name', name: 'contacts.name'},
            {data: 'email', name: 'contacts.email'},
            {data: 'phone', name: 'contacts.phone'},
            {data: 'type', name: 'contacts.type'},
            {data: 'attribute_1', name: 'attribute_1', orderable: false, searchable: false},
            {data: 'attribute_2', name: 'attribute_2', orderable: false, searchable: false},
            {data: 'attribute_3', name: 'attribute_3', orderable: false, searchable: false},
            {data: 'attribute_4', name: 'attribute_4', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        );
        
        var table = $('#contacts_list').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: {
                url: "{{ route('contacts.index') }}",
                data: function(d) {
                    d.debtor_id = $('#debtorFilter').val();
                }
            },
            order: [[1, "desc"]],
            columns: columns
        });

        $('#debtorFilter').change(function() {
            table.ajax.reload();
        });

        $('#selectAll').click(function() {
            $('.contact-checkbox').prop('checked', this.checked);
            toggleBulkDeleteBtn();
        });

        $(document).on('change', '.contact-checkbox', function() {
            toggleBulkDeleteBtn();
        });

        $('#bulkDeleteBtn').click(function() {
            if ($('.contact-checkbox:checked').length === 0) {
                alert('Please select at least one contact');
                return;
            }
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete selected contacts",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var ids = [];
                    $('.contact-checkbox:checked').each(function() {
                        ids.push($(this).val());
                    });
                    
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('contacts.bulk-delete') }}",
                        data: {
                            ids: ids,
                            _token: "{{ csrf_token() }}"
                        },
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
            if ($('.contact-checkbox:checked').length > 0) {
                $('#bulkDeleteBtn').show();
            } else {
                $('#bulkDeleteBtn').hide();
            }
        }

        function deleteContact(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this contact",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'DELETE',
                        url: "{{ route('contacts.index') }}/" + id,
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
