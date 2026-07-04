@extends('app.layout.app')
@section('page_title')
    {{ isset($sesVerifiedEmail) ? 'Edit' : 'Add' }} SES Verified Email
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($sesVerifiedEmail) ? 'Edit' : 'Add' }} SES Verified Email</h3>
                </div>
                <form action="{{ isset($sesVerifiedEmail) ? route('ses-verified-emails.update', $sesVerifiedEmail->id) : route('ses-verified-emails.store') }}" method="POST">
                    @csrf
                    @if(isset($sesVerifiedEmail))
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $sesVerifiedEmail->email ?? '') }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="active_status" class="form-control @error('active_status') is-invalid @enderror" required>
                                <option value="Y" {{ old('active_status', $sesVerifiedEmail->active_status ?? 'Y') === 'Y' ? 'selected' : '' }}>Active</option>
                                <option value="N" {{ old('active_status', $sesVerifiedEmail->active_status ?? '') === 'N' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('active_status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('ses-verified-emails.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
