{{-- resources/views/teacher/quizzes/create.blade.php --}}
@extends('teacher.layout')

@section('title', 'Tạo Quiz mới')
@section('quizzes-active', 'active')
@section('page-icon', 'plus-circle')
@section('page-title', 'Tạo Quiz mới')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.quizzes.index') }}">Quiz</a></li>
            <li class="breadcrumb-item active">Tạo mới</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Thông tin quiz</h5>
                </div>
                <div class="card-body">
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('teacher.quizzes.store') }}" method="POST" id="quizForm">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề quiz <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="VD: Kiểm tra chương 1" required>
                        </div>

                        <div class="mb-4">
                            <label for="lesson_id" class="form-label">Bài học <span class="text-danger">*</span></label>
                            <select class="form-control" id="lesson_id" name="lesson_id" required>
                                <option value="">-- Chọn bài học --</option>
                                @foreach($lessons as $lesson)
                                    <option value="{{ $lesson->id }}">
                                        [{{ $lesson->course->title }}] {{ $lesson->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="bi bi-question-circle me-2 text-primary"></i>Danh sách câu hỏi</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAdd">
                                <i class="bi bi-plus-circle"></i> Thêm câu hỏi
                            </button>
                        </div>

                        <div id="questions-container"></div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">Lưu quiz</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Code JavaScript đơn giản nhất
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded!');
    
    let questionCount = 0;
    
    function addQuestion() {
        console.log('Adding question:', questionCount);
        
        const container = document.getElementById('questions-container');
        if (!container) {
            alert('Container not found!');
            return;
        }
        
        const questionDiv = document.createElement('div');
        questionDiv.className = 'card mb-3 p-3';
        questionDiv.id = 'q_' + questionCount;
        questionDiv.innerHTML = `
            <div class="d-flex justify-content-between mb-2">
                <h6>Câu hỏi ${questionCount + 1}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.card').remove()">Xóa</button>
            </div>
            <input type="text" class="form-control mb-3" name="questions[${questionCount}][text]" placeholder="Nội dung câu hỏi" required>
            
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">A</span>
                        <input type="text" class="form-control" name="questions[${questionCount}][options][0]" placeholder="Đáp án A" required>
                        <div class="input-group-text">
                            <input type="radio" name="questions[${questionCount}][correct]" value="0" checked>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">B</span>
                        <input type="text" class="form-control" name="questions[${questionCount}][options][1]" placeholder="Đáp án B" required>
                        <div class="input-group-text">
                            <input type="radio" name="questions[${questionCount}][correct]" value="1">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">C</span>
                        <input type="text" class="form-control" name="questions[${questionCount}][options][2]" placeholder="Đáp án C" required>
                        <div class="input-group-text">
                            <input type="radio" name="questions[${questionCount}][correct]" value="2">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">D</span>
                        <input type="text" class="form-control" name="questions[${questionCount}][options][3]" placeholder="Đáp án D" required>
                        <div class="input-group-text">
                            <input type="radio" name="questions[${questionCount}][correct]" value="3">
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(questionDiv);
        questionCount++;
    }
    
    // Gán sự kiện click cho button
    const btn = document.getElementById('btnAdd');
    if (btn) {
        btn.onclick = addQuestion;
    } else {
        console.error('Button not found!');
    }
    
    // Thêm 2 câu hỏi mẫu
    addQuestion();
    addQuestion();
});
</script>
@endsection