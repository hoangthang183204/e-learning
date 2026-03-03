@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card text-bg-primary mb-3">
            <div class="card-body">
                <a class="btn btn-success" href="/admin/users" class="create-btn">Users</a>
                <p class="card-text">Quản lý người dùng</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Courses</h5>
                <p class="card-text">Quản lý khoá học</p>
            </div>
        </div>
    </div>
</div>
@endsection