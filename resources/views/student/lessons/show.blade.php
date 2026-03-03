@extends('student.layout')

@section('content')
    <h4>{{ $lesson->title }}</h4>

    @if ($lesson->video_url)
        <div class="ratio ratio-16x9 mb-3">
            <iframe src="https://www.youtube-nocookie.com/embed/{{ $lesson->video_url }}" frameborder="0" allowfullscreen>
            </iframe>
        </div>
        {{-- QUIZ --}}
        @if ($quiz)
            <hr>
            <h5>📝 Bài kiểm tra</h5>

            <p>{{ $quiz->title }}</p>

            <a href="{{ route('student.quiz.show', $quiz->id) }}" class="btn btn-warning">
                Làm quiz
            </a>
        @endif
    @endif

    <div class="mb-4">
        {!! nl2br(e($lesson->content)) !!}
    </div>

    <div class="d-flex justify-content-between">
        @if ($prevLesson)
            <a class="btn btn-secondary" href="{{ route('student.lessons.show', $prevLesson) }}">
                ← Bài trước
            </a>
        @else
            <span></span>
        @endif

        @if ($nextLesson)
            <a class="btn btn-primary" href="{{ route('student.lessons.show', $nextLesson) }}">
                Bài tiếp →
            </a>
        @endif
    </div>

    @if (!$isCompleted)
        <form method="POST" action="{{ route('lessons.complete', $lesson->id) }}">
            @csrf
            <button class="btn btn-success" type="submit">
                ✅ Hoàn thành bài học
            </button>
        </form>
    @else
        <div class="alert alert-success mt-3">
            ✔ Bạn đã hoàn thành bài này
        </div>
    @endif
@endsection
