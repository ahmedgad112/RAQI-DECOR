@extends('layouts.admin')

@section('title', 'إضافة مستخدم')

@section('content')
@include('admin.users._form', [
    'user' => null,
    'action' => route('admin.users.store'),
    'method' => 'POST',
    'permissionOptions' => $permissionOptions,
])
@endsection
