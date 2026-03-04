{{-- resources/views/admin/quizzes/questions.blade.php --}}
@extends('admin.layout')

@section('title', 'Quản lý câu hỏi - ' . $quiz->title)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                Quản lý câu hỏi: <span class="text-primary">{{ $quiz->title }}</span>
                <small class="text-muted">({{ $quiz->lesson->course->title }} - {{ $quiz->lesson->title }})</small>
            </h1>
            <div>
                <a href="{{ route('admin.quizzes.index', ['lesson_id' => $quiz->lesson_id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            {{-- Form thêm câu hỏi --}}
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Thêm câu hỏi mới</h5>
                    </div>
                    <div class="card-body">
                        <form id="questionForm" action="{{ route('admin.quizzes.questions.store', $quiz->id) }}"
                            method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Câu hỏi <span class="text-danger">*</span></label>
                                <textarea name="question" class="form-control" rows="2" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Điểm số <span class="text-danger">*</span></label>
                                <input type="number" name="points" class="form-control" value="1" min="1"
                                    max="10" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Đáp án <span class="text-danger">*</span></label>
                                <div id="options-container">
                                    <div class="input-group mb-2">
                                        <input type="text" name="options[0][text]" class="form-control"
                                            placeholder="Đáp án A" required>
                                        <div class="input-group-text">
                                            <input type="radio" name="correct_answer" value="0" checked>
                                        </div>
                                    </div>
                                    <div class="input-group mb-2">
                                        <input type="text" name="options[1][text]" class="form-control"
                                            placeholder="Đáp án B" required>
                                        <div class="input-group-text">
                                            <input type="radio" name="correct_answer" value="1">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-success mt-2" onclick="addOption()">
                                    <i class="fas fa-plus"></i> Thêm đáp án
                                </button>
                            </div>

                            <button type="submit" class="btn btn-primary">Thêm câu hỏi</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Danh sách câu hỏi --}}
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Danh sách câu hỏi ({{ $quiz->questions->count() }})</h5>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        @forelse($quiz->questions as $index => $question)
                            <div
                                class="card mb-3 border-{{ $question->options->where('is_correct', true)->first() ? 'success' : 'danger' }}">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <strong>Câu {{ $index + 1 }} ({{ $question->points }} điểm)</strong>
                                    <div>
                                        <button class="btn btn-sm btn-warning" onclick="editQuestion({{ $question->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Xóa câu hỏi này?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p>{{ $question->question }}</p>
                                    @foreach ($question->options as $optIndex => $option)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" disabled
                                                {{ $option->is_correct ? 'checked' : '' }}>
                                            <label
                                                class="form-check-label {{ $option->is_correct ? 'text-success fw-bold' : '' }}">
                                                {{ chr(65 + $optIndex) }}. {{ $option->option_text }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">Chưa có câu hỏi nào</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let optionCount = 2;

            function addOption() {
                if (optionCount >= 6) {
                    alert('Chỉ được thêm tối đa 6 đáp án');
                    return;
                }

                const container = document.getElementById('options-container');
                const html = `
        <div class="input-group mb-2">
            <input type="text" name="options[${optionCount}][text]" class="form-control" placeholder="Đáp án ${String.fromCharCode(65 + optionCount)}" required>
            <div class="input-group-text">
                <input type="radio" name="correct_answer" value="${optionCount}">
            </div>
        </div>
    `;
                container.insertAdjacentHTML('beforeend', html);
                optionCount++;
            }

            function editQuestion(questionId) {
                fetch(`/admin/questions/${questionId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Populate form với dữ liệu câu hỏi
                        console.log(data);
                        alert('Tính năng đang phát triển');
                    })
                    .catch(error => console.error('Error:', error));
            }
        </script>
    @endpush
@endsection
