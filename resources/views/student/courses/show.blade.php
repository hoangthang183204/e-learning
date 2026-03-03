@extends('student.layout')

@section('content')
    <h3>{{ $course->title }}</h3>
    @if (!auth()->user()->courses->contains($course->id))
        <div class="alert alert-warning">
            Bạn cần đăng ký khoá học để xem bài học
        </div>
    @else
        <h4>Tiến độ khóa học</h4>

        <div class="progress mb-3" style="height: 25px;">
            <div class="progress-bar bg-success" style="width: {{ $progress }}%">
                {{ $progress }}%
            </div>
        </div>
        <p>
            Đã học {{ $completedLessons }} / {{ $totalLessons }} bài
        </p>

        @foreach ($course->lessons as $lesson)
            <li class="list-group-item d-flex justify-content-between">
                <a href="{{ route('student.lessons.show', $lesson) }}">
                    {{ $lesson->order_number }}. {{ $lesson->title }}
                </a>

                @if (auth()->user()->completedLessons->contains($lesson->id))
                    <span class="text-success">✔</span>
                @endif
            </li>
        @endforeach
    @endif

    @php
        $isEnrolled = auth()->user()->courses->contains($course->id);
    @endphp

    @if (!$isEnrolled)
        {{-- ENROLL --}}
        <form method="POST" action="{{ route('student.courses.enroll', $course->id) }}">
            @csrf
            <button class="btn btn-success">
                Đăng ký khóa học
            </button>
        </form>
    @else
        {{-- UNENROLL --}}
        <form method="POST" action="{{ route('student.courses.unenroll', $course->id) }}">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger">
                Hủy đăng ký
            </button>
        </form>
    @endif

@endsection
