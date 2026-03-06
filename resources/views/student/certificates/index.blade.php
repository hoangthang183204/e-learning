@extends('student.layout')

@section('title', 'Chứng chỉ của tôi')
@section('page-icon', 'award')
@section('page-title', 'Chứng chỉ của tôi')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Chứng chỉ được cấp khi bạn hoàn thành 100% bài học của khóa học.
        </div>
    </div>

    @forelse($completedCourses as $course)
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="bi bi-award-fill text-success fs-3"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $course->title }}</h5>
                            <small class="text-secondary">
                                <i class="bi bi-calendar-check me-1"></i>
                                Hoàn thành: {{ \Carbon\Carbon::parse($course->pivot->completed_at)->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                    
                    @if(isset($certificates[$course->id]))
                        <div class="alert alert-success py-2 mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            Đã cấp chứng chỉ
                            <span class="badge bg-light text-dark ms-2">{{ $certificates[$course->id]->certificate_number }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('student.certificates.show', $course) }}" class="btn btn-outline-primary flex-grow-1">
                                <i class="bi bi-eye"></i> Xem chứng chỉ
                            </a>
                            <a href="{{ route('student.certificates.download', $course) }}" class="btn btn-success flex-grow-1">
                                <i class="bi bi-download"></i> Tải PDF
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning py-2 mb-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Chứng chỉ đang được tạo...
                        </div>
                        <a href="{{ route('student.certificates.show', $course) }}" class="btn btn-primary w-100">
                            <i class="bi bi-award"></i> Xem chứng chỉ
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-award fs-1 text-secondary mb-3"></i>
                <h5 class="mb-2">Bạn chưa có chứng chỉ nào</h5>
                <p class="text-secondary mb-4">Hoàn thành khóa học để nhận chứng chỉ</p>
                <a href="{{ route('student.courses.index') }}" class="btn btn-primary">
                    <i class="bi bi-book"></i> Khám phá khóa học
                </a>
            </div>
        </div>
    @endforelse
</div>
@endsection