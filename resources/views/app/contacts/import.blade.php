@extends('app.layout.app')
@section('page_title')
    Import Contacts
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Import Contacts</h3>
                </div>
                <form action="{{ route('contacts.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        @if(auth()->user()->type != '1')
                        <div class="form-group">
                            <label>Corporate Debtor</label>
                            <select name="user_id" class="form-control" required>
                                <option value="">Select Corporate Debtor</option>
                                @foreach($corporateDebtors as $debtor)
                                    <option value="{{ $debtor->id }}" {{ old('user_id') == $debtor->id ? 'selected' : '' }}>
                                        {{ $debtor->name }} ({{ $debtor->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        @endif
                        <div class="form-group">
                            <label>CSV File</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                            <small class="form-text text-muted">
                                <a href="{{ route('contacts.import.sample') }}" class="btn btn-sm btn-info mt-2">
                                    <i class="fas fa-download"></i> Download Sample CSV
                                </a>
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Import</button>
                        <a href="{{ route('contacts.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
