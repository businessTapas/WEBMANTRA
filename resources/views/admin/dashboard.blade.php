@extends('admin.layout.app')
@section('content')

<div>
    <h2>Admin Dashboard</h2>
    <a href="{{route('admin.logout')}}" >Logout</a>
</div>
@endsection