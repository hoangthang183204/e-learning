@extends('student.layout')

@section('content')
<h3>Khoá học</h3>

<div class="row">
@foreach ($courses as $course)
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5>{{ $course->title }}</h5>
                <p>{{ Str::limit($course->description, 100) }}</p>

                <a href="{{ route('student.courses.show', $course) }}"
                   class="btn btn-primary btn-sm">
                   Vào học
                </a>
            </div>
        </div>
    </div>
@endforeach
</div>
@endsection