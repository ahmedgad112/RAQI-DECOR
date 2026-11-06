@extends('layouts.admin')

@section('title', 'إضافة فئة')

@section('content')
@include('admin.categories._form', ['category' => null, 'action' => route('admin.categories.store'), 'method' => 'POST'])
@endsection
