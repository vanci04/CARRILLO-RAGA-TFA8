@extends('layouts.app')
@php($isCustomer = $kind === 'customers')
@section('title', ($record->exists ? 'Edit ' : 'Create ').($isCustomer ? 'customer' : 'user'))
@section('content')
<a class="back-link" href="{{ route($kind.'.index') }}">← Back to {{ $isCustomer ? 'customers' : 'staff accounts' }}</a>
<div class="page-heading form-page-heading"><div><p class="eyebrow">{{ $record->exists ? 'KEEP THE DETAILS CURRENT' : 'WELCOME SOMEONE NEW' }}</p><h1>{{ $record->exists ? 'Update the account.' : 'Create an account.' }}</h1><p>Required fields are checked before anything is saved.</p></div></div>
<section class="account-form-card">
@if($errors->any())<div class="form-errors" role="alert"><strong>Please check the highlighted fields.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="post" action="{{ $record->exists ? route($kind.'.update', $record) : route($kind.'.store') }}" @if(!$isCustomer) enctype="multipart/form-data" @endif>
    @csrf @if($record->exists) @method('PUT') @endif
    <label>Full name <input name="full_name" value="{{ old('full_name', $record->full_name) }}" required maxlength="120"></label>
    @if($isCustomer)
        <label>Email address <input type="email" name="email" value="{{ old('email', $record->email) }}" required maxlength="255"></label>
        <label>Phone number <input name="phone" value="{{ old('phone', $record->phone) }}" maxlength="30"></label>
    @else
        <label>Username <input name="username" value="{{ old('username', $record->username) }}" required maxlength="80"></label>
        <label>Email address <input type="email" name="email" value="{{ old('email', $record->email) }}" required maxlength="255"></label>
        <label>Role <select name="role" required>@foreach(['Manager','Barista','Cashier','Kitchen'] as $role)<option value="{{ $role }}" @selected(old('role', $record->role) === $role)>{{ $role }}</option>@endforeach</select></label>
        <label>{{ $record->exists ? 'New password (optional)' : 'Password' }} <input type="password" name="password" @required(!$record->exists) autocomplete="new-password"></label>
        <label>Confirm password <input type="password" name="password_confirmation" @required(!$record->exists) autocomplete="new-password"></label>
        @if($record->exists)<label>Profile picture <input type="file" name="avatar" accept="image/*"><small>Image files only, up to 2 MB. A 240 × 240 JPEG thumbnail will be prepared automatically.</small></label>@endif
    @endif
    <div class="form-actions"><a class="button light" href="{{ route($kind.'.index') }}">Cancel</a><button class="button dark" type="submit">{{ $record->exists ? 'Save changes' : 'Create account' }}</button></div>
</form>
</section>
@endsection
