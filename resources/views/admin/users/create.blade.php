@extends('admin.layout')

@section('content')
<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    <input class="form-control mb-2" name="name" placeholder="Tên">
    <input class="form-control mb-2" name="email" placeholder="Email">
    <input type="password" class="form-control mb-2" name="password" placeholder="Mật khẩu">

    <select class="form-control mb-2" name="role">
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
        <option value="admin">Admin</option>
    </select>

    <button class="btn btn-success">Lưu</button>
</form>
@endsection