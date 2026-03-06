{{-- resources/views/student/courses/partials/course-card.blade.php --}}
<div class="col-md-4 mb-4">
    <div class="card h-100 border-0 shadow-sm">
        @if($course->thumbnail)
            <img src="{{ asset('storage/'.$course->thumbnail) }}" class="card-img-top" alt="{{ $course->title }}" style="height: 160px; object-fit: cover;">
        @else
            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 160px; background: linear-gradient(135deg, #4158D0, #C850C0)">
                <i class="bi bi-image text-white fs-1"></i>
            </div>
        @endif
        <div class="card-body">
            <h5 class="card-title">{{ $course->title }}</h5>
            <p class="card-text text-secondary small">{{ Str::limit($course->description, 60) }}</p>
            
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-secondary">
                    <i class="bi bi-people me-1"></i> {{ $course->students_count }} học viên
                </small>
                <small class="text-secondary">
                    <i class="bi bi-collection me-1"></i> {{ $course->lessons_count }} bài
                </small>
            </div>
            
            @if($course->teacher)
                <small class="text-secondary d-block mb-3">
                    <i class="bi bi-person-circle me-1"></i> {{ $course->teacher->name }}
                </small>
            @endif
            
            @if(isset($course->enrollment_status) && $course->enrollment_status == 'approved')
                @php
                    $user = auth()->user();
                    $completedLessons = $user->completedLessons()
                        ->whereIn('lesson_id', $course->lessons->pluck('id'))
                        ->count();
                    $progress = $course->lessons->count() > 0 
                        ? round(($completedLessons / $course->lessons->count()) * 100) 
                        : 0;
                @endphp
                <div class="mb-2">
                    <div class="d-flex justify-content-between small">
                        <span>Tiến độ</span>
                        <span>{{ $progress }}%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            @endif
            
            <a href="{{ route('student.courses.show', $course) }}" class="btn btn-outline-primary w-100">
                <i class="bi bi-eye me-1"></i> 
                @if(isset($course->enrollment_status) && $course->enrollment_status == 'approved')
                    Học tiếp
                @else
                    Xem chi tiết
                @endif
            </a>
        </div>
    </div>
</div>