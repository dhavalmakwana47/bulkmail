@extends('app.layout.app')
@section('page_title')
    {{ isset($debtorAttachment) ? 'Edit' : 'Add' }} Attachment
@endsection
@section('header-script')
<style>
  
</style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($debtorAttachment) ? 'Edit' : 'Add' }} Attachment</h3>
                </div>
                <form action="{{ isset($debtorAttachment) ? route('debtor-attachments.update', $debtorAttachment->id) : route('debtor-attachments.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($debtorAttachment))
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        @if(auth()->user()->type != '1')
                        <div class="form-group">
                            <label>Corporate Debtor</label>
                            <select name="user_id" class="form-control select2" required>
                                <option value="">Select Corporate Debtor</option>
                                @foreach($corporateDebtors as $debtor)
                                    <option value="{{ $debtor->id }}" {{ old('user_id', $debtorAttachment->user_id ?? '') == $debtor->id ? 'selected' : '' }}>
                                        {{ $debtor->name }} ({{ $debtor->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        @endif
                        <div class="form-group">
                            <label>Attachment Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $debtorAttachment->name ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>File</label>
                            <input type="file" name="file" class="form-control" {{ isset($debtorAttachment) ? '' : 'required' }}>
                            @if(isset($debtorAttachment))
                                <small class="text-muted">Current file: {{ $debtorAttachment->file_name }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('debtor-attachments.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select Corporate Debtor',
                allowClear: true
            });
        });
    </script>
@endsection
