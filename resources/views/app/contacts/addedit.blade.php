@extends('app.layout.app')
@section('page_title')
    {{ isset($contact) ? 'Edit' : 'Add' }} Contact
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($contact) ? 'Edit' : 'Add' }} Contact</h3>
                </div>
                <form action="{{ isset($contact) ? route('contacts.update', $contact->id) : route('contacts.store') }}" method="POST">
                    @csrf
                    @if(isset($contact))
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        @if(auth()->user()->type != '1')
                        <div class="form-group">
                            <label>Corporate Debtor</label>
                            <select name="user_id" class="form-control" required>
                                <option value="">Select Corporate Debtor</option>
                                @foreach($corporateDebtors as $debtor)
                                    <option value="{{ $debtor->id }}" {{ old('user_id', $contact->user_id ?? '') == $debtor->id ? 'selected' : '' }}>
                                        {{ $debtor->name }} ({{ $debtor->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        @endif
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $contact->name ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $contact->email ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $contact->phone ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="MEMBER" {{ old('type', isset($contact) ? (is_string($contact->type) ? $contact->type : $contact->type->value) : '') == 'MEMBER' ? 'selected' : '' }}>MEMBER</option>
                                <option value="OTHER" {{ old('type', isset($contact) ? (is_string($contact->type) ? $contact->type : $contact->type->value) : '') == 'OTHER' ? 'selected' : '' }}>OTHER</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Attribute 1</label>
                            <input type="text" name="attributes[attribute_1]" class="form-control" value="{{ old('attributes.attribute_1', isset($contact) ? ($contact->attributes->where('key', 'attribute_1')->first()->value ?? '') : '') }}">
                        </div>
                        <div class="form-group">
                            <label>Attribute 2</label>
                            <input type="text" name="attributes[attribute_2]" class="form-control" value="{{ old('attributes.attribute_2', isset($contact) ? ($contact->attributes->where('key', 'attribute_2')->first()->value ?? '') : '') }}">
                        </div>
                        <div class="form-group">
                            <label>Attribute 3</label>
                            <input type="text" name="attributes[attribute_3]" class="form-control" value="{{ old('attributes.attribute_3', isset($contact) ? ($contact->attributes->where('key', 'attribute_3')->first()->value ?? '') : '') }}">
                        </div>
                        <div class="form-group">
                            <label>Attribute 4</label>
                            <input type="text" name="attributes[attribute_4]" class="form-control" value="{{ old('attributes.attribute_4', isset($contact) ? ($contact->attributes->where('key', 'attribute_4')->first()->value ?? '') : '') }}">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('contacts.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
