@extends('app.layout.app')
@section('page_title')
    Contact Details
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 21px;">Contact Details</h3>
                    <div class="float-right">
                        <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Back</a>
                        <a href="{{ route('contacts.edit', $contact->id) }}" class="btn btn-info">Edit</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID</th>
                                    <td>{{ $contact->id }}</td>
                                </tr>
                                @if(auth()->user()->type != '1')
                                <tr>
                                    <th>Corporate Debtor</th>
                                    <td>{{ $contact->user->name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $contact->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $contact->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $contact->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ is_string($contact->type) ? $contact->type : $contact->type->value }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Attribute 1</th>
                                    <td>{{ $contact->attributes->where('key', 'attribute_1')->first()->value ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Attribute 2</th>
                                    <td>{{ $contact->attributes->where('key', 'attribute_2')->first()->value ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Attribute 3</th>
                                    <td>{{ $contact->attributes->where('key', 'attribute_3')->first()->value ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Attribute 4</th>
                                    <td>{{ $contact->attributes->where('key', 'attribute_4')->first()->value ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $contact->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $contact->updated_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
