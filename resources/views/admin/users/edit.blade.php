@extends('layouts.admin')

@section('title', 'تعديل مستخدم')

@section('content')
@include('admin.users._form', [
    'user' => $user,
    'action' => route('admin.users.update', $user),
    'method' => 'PUT',
    'permissionOptions' => $permissionOptions,
])
@endsection
