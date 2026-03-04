{{-- resources/views/student/courses/partials/course-content.blade.php --}}

<h4 class="mt-4">Tiến độ khóa học</h4>
<div class="progress mb-3" style="height: 25px;">
    <div class="progress-bar bg-success" 
         role="progressbar" 
         style="width: {{ $progress }}%;" 
         aria-valuenow="{{ $progress }}" 
         aria-valuemin="0" 
         aria-valuemax="100">
        {{ $progress }}%
    </div>
</div>
<p>Đã học <strong>{{ $completedLessons }}</strong> / <strong>{{ $totalLessons }}</strong> bài</p>

<h4>Danh sách bài học</h4>
<ul class="list-group">
    @foreach ($course->lessons as $lesson)
        @php
            $isLessonCompleted = auth()->user()->completedLessons()
                ->where('lesson_id', $lesson->id)
                ->exists();
        @endphp
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('student.lessons.show', $lesson) }}">
                    {{ $lesson->order_number }}. {{ $lesson->title }}
                </a>
                @if($lesson->duration)
                    <small class="text-muted ms-2">
                        <i class="bi bi-clock"></i> {{ $lesson->duration }} phút
                    </small>
                @endif
            </div>
            @if($isLessonCompleted)
                <span class="badge bg-success">
                    <i class="bi bi-check-circle"></i> Đã học
                </span>
            @else
                <span class="badge bg-secondary">Chưa học</span>
            @endif
        </li>
    @endforeach
</ul>