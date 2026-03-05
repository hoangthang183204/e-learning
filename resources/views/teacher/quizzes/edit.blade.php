{{-- resources/views/teacher/quizzes/edit.blade.php --}}
@extends('teacher.layout')

@section('title', 'Sửa Quiz')
@section('quizzes-active', 'active')
@section('page-icon', 'pencil')
@section('page-title', 'Sửa Quiz')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.quizzes.index') }}">Quiz</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.quizzes.show', $quiz) }}">{{ $quiz->title }}</a></li>
            <li class="breadcrumb-item active">Sửa</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Chỉnh sửa quiz</h5>
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

                    <form action="{{ route('teacher.quizzes.update', $quiz) }}" method="POST" id="quizForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề quiz <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $quiz->title) }}"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="lesson_id" class="form-label">Bài học <span class="text-danger">*</span></label>
                            <select class="form-control @error('lesson_id') is-invalid @enderror" 
                                    id="lesson_id" 
                                    name="lesson_id" 
                                    required>
                                <option value="">-- Chọn bài học --</option>
                                @foreach($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" 
                                        {{ old('lesson_id', $quiz->lesson_id) == $lesson->id ? 'selected' : '' }}>
                                        [{{ $lesson->course->title }}] {{ $lesson->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="bi bi-question-circle me-2 text-primary"></i>Danh sách câu hỏi</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddQuestion">
                                <i class="bi bi-plus-circle"></i> Thêm câu hỏi
                            </button>
                        </div>

                        <div id="questions-container">
                            @foreach($quiz->questions as $qIndex => $question)
                                <div class="card mb-3 p-3" id="question-{{ $qIndex }}">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h6>Câu hỏi {{ $qIndex + 1 }}</h6>
                                        @if($quiz->questions->count() > 1)
                                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.card').remove()">Xóa</button>
                                        @endif
                                    </div>
                                    
                                    <input type="hidden" name="questions[{{ $qIndex }}][id]" value="{{ $question->id }}">
                                    <input type="text" class="form-control mb-3" name="questions[{{ $qIndex }}][text]" value="{{ $question->question }}" placeholder="Nội dung câu hỏi" required>
                                    
                                    <div class="row">
                                        @foreach($question->options as $oIndex => $option)
                                            <div class="col-md-6 mb-2">
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ chr(65 + $oIndex) }}</span>
                                                    <input type="text" class="form-control" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][text]" value="{{ $option->option_text }}" placeholder="Đáp án {{ chr(65 + $oIndex) }}" required>
                                                    <input type="hidden" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][id]" value="{{ $option->id }}">
                                                    <div class="input-group-text">
                                                        <input type="radio" name="questions[{{ $qIndex }}][correct]" value="{{ $oIndex }}" {{ $option->is_correct ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Thông tin</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Ngày tạo</label>
                        <div class="fw-bold">{{ $quiz->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Số câu hỏi</label>
                        <div class="fw-bold">{{ $quiz->questions->count() }}</div>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Lưu ý:</strong> Thay đổi câu hỏi có thể ảnh hưởng đến kết quả cũ.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Code JavaScript đồng bộ với create.blade.php
document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit script loaded!');
    
    let questionCount = {{ $quiz->questions->count() }};
    
    function addQuestion() {
        console.log('Adding question:', questionCount);
        
        const container = document.getElementById('questions-container');
        if (!container) {
            alert('Container not found!');
            return;
        }
        
        const questionDiv = document.createElement('div');
        questionDiv.className = 'card mb-3 p-3';
        questionDiv.id = 'new_question_' + questionCount;
        questionDiv.innerHTML = `
            <div class="d-flex justify-content-between mb-2">
                <h6>Câu hỏi mới ${questionCount + 1}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.card').remove()">Xóa</button>
            </div>
            <input type="text" class="form-control mb-3" name="new_questions[${questionCount}][text]" placeholder="Nội dung câu hỏi" required>
            
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">A</span>
                        <input type="text" class="form-control" name="new_questions[${questionCount}][options][0]" placeholder="Đáp án A" required>
                        <div class="input-group-text">
                            <input type="radio" name="new_questions[${questionCount}][correct]" value="0" checked>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">B</span>
                        <input type="text" class="form-control" name="new_questions[${questionCount}][options][1]" placeholder="Đáp án B" required>
                        <div class="input-group-text">
                            <input type="radio" name="new_questions[${questionCount}][correct]" value="1">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">C</span>
                        <input type="text" class="form-control" name="new_questions[${questionCount}][options][2]" placeholder="Đáp án C" required>
                        <div class="input-group-text">
                            <input type="radio" name="new_questions[${questionCount}][correct]" value="2">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <span class="input-group-text">D</span>
                        <input type="text" class="form-control" name="new_questions[${questionCount}][options][3]" placeholder="Đáp án D" required>
                        <div class="input-group-text">
                            <input type="radio" name="new_questions[${questionCount}][correct]" value="3">
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(questionDiv);
        questionCount++;
    }
    
    // Gán sự kiện click cho button thêm câu hỏi
    const btnAdd = document.getElementById('btnAddQuestion');
    if (btnAdd) {
        btnAdd.onclick = addQuestion;
        console.log('Add button attached');
    } else {
        console.error('Add button not found!');
    }
});
</script>
@endsection