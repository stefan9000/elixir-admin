@extends('_layout.admin.master')
@section('content')
<div class="app-container">
    <div class="app-heading">
        <a class="back"  href="{{ route('admin_regular_index') }}">Back to list</a>
    </div>
    
    <ul class="info-ul">
        <li>First name: <span>{{ $regular->first_name }}</span></li>
        <li>Last name: <span>{{ $regular->last_name }}</span></li>
        <li>Email: <span>{{ $regular->email }}</span></li>
        <li>Phone: <span>{{ $regular->phone }}</span></li>
        <li>Date of birth: <span>{{ $regular->date_of_birth }}</span></li>
        <li>Provider: <span>{{ $regular->provider }}</span></li>
    </ul>
</div>
@endsection
