@extends('app.layout.app')
@section('page_title')
    {{ isset($user) ? 'Edit' : 'Add' }} Corporate Debtor
@endsection
@section('header-script')
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        Corporate Debtor {{ isset($user) ? 'Edit' : 'Add' }}
                    </h3>
                </div>
                <form action="{{ isset($user) ? route('corporate-debtors.update', $user->id) : route('corporate-debtors.store') }}" method="post">
                    @csrf
                    @if (isset($user))
                    @method('PATCH')
                    <input type="hidden" value="{{ $user->id }}" name="id">
                    @endif
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name', $user->name ?? '') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email', $user->email ?? '') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password {!! isset($user) ? '' : '<span class="text-danger">*</span>' !!}</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        name="password" {{ isset($user) ? '' : 'required' }}>
                                    @if(isset($user))
                                        <small class="text-muted">Leave blank to keep current password</small>
                                    @endif
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation">
                                    @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="1" {{ (old('type', $user->type ?? 1) == 1) ? 'selected' : '' }}>Corporate Debtor</option>
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Active Status</label>
                                    <div>
                                        <input type="checkbox" data-bootstrap-switch="" name="is_active"
                                            {{ (isset($user) && $user->is_active) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Allow Duplicate Email</label>
                                    <div>
                                        <input type="checkbox" data-bootstrap-switch="" name="duplicate_email"
                                            {{ (isset($user) && $user->duplicate_email) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" name="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('corporate-debtors.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script>
        $("input[data-bootstrap-switch]").each(function() {
            $(this).bootstrapSwitch();
        })
    </script>
@endsection
