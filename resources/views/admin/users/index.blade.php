@extends('admin.layout')

@section('title', 'Quản lý User')

@section('content')
<a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">+ Thêm user</a>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Tên</th>
        <th>Email</th>
        <th>Role</th>
        <th>Hành động</th>
    </tr>

    @foreach($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ ucfirst($user->role) }}</td>
        <td>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">Sửa</a>

            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger"
                    onclick="return confirm('Xoá user này?')">Xoá</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

{{ $users->links() }}
@endsection