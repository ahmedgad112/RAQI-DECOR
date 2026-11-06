@extends('layouts.admin')

@section('title', 'تعديل فئة')

@section('content')
@include('admin.categories._form', ['category' => $category, 'action' => route('admin.categories.update', $category), 'method' => 'PUT'])
@endsection
