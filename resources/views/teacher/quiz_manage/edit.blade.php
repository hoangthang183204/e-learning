{{-- resources/views/teacher/quizzes/edit.blade.php --}}
@extends('teacher.layout')

@section('title', 'Sửa Quiz')

@section('quizzes-active', 'active')
@section('page-icon', 'pencil-square')
@section('page-title', 'Sửa Quiz')

@section('content')
    <!-- Breadcrumb -->

    <!-- Form Container -->
    <div class="form-container">
        <!-- Progress Steps -->
        <div class="form-progress">
            <div class="progress-steps">
                <div class="step active">
                    <span class="step-number">1</span>
                    <span>Thông tin cơ bản</span>
                </div>
                <div class="step active">
                    <span class="step-number">2</span>
                    <span>Chỉnh sửa câu hỏi</span>
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    <span>Cài đặt & Cập nhật</span>
                </div>
            </div>
        </div>

        <!-- Form Content -->
        <div class="form-content">
            <form method="POST" action="{{ route('teacher.quizzes.update', $quiz) }}" id="quizForm">
                @csrf
                @method('PUT')

                <!-- Quiz Info Section -->
                <div class="form-group">
                    <label><i class="bi bi-tag"></i> Tiêu đề quiz <span class="required"></span></label>
                    <input type="text" 
                           name="title" 
                           placeholder="Nhập tiêu đề quiz..." 
                           value="{{ old('title', $quiz->title) }}" 
                           required>
                    @error('title')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label><i class="bi bi-book"></i> Bài học liên quan <span class="required"></span></label>
                    <select name="lesson_id" required>
                        <option value="">-- Chọn bài học --</option>
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}" 
                                {{ old('lesson_id', $quiz->lesson_id) == $lesson->id ? 'selected' : '' }}>
                                {{ $lesson->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('lesson_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label><i class="bi bi-clock"></i> Thời gian làm bài (phút)</label>
                    <input type="number" 
                           name="duration" 
                           placeholder="Nhập thời gian làm bài..." 
                           value="{{ old('duration', $quiz->duration ?? 15) }}" 
                           min="1" 
                           max="180">
                    <div class="help-text">
                        <i class="bi bi-info-circle"></i> Để trống nếu không giới hạn thời gian
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="section-divider">
                    <hr>
                    <h3><i class="bi bi-question-circle"></i> Câu hỏi</h3>
                    <hr>
                </div>

                <!-- Questions Container -->
                <div id="questions-container">
                    @foreach($quiz->questions as $qIndex => $question)
                        <div class="question-card" id="question-{{ $qIndex }}" data-question-id="{{ $question->id }}">
                            @if($qIndex >= 1)
                                <button type="button" class="remove-question" onclick="removeQuestion({{ $qIndex }})">
                                    <i class="bi bi-x"></i>
                                </button>
                            @endif
                            
                            <div class="question-header">
                                <h4>Câu hỏi {{ $qIndex + 1 }}</h4>
                                <span class="question-badge">ID: {{ $question->id }}</span>
                            </div>
                            
                            <div class="form-group">
                                <label>Nội dung câu hỏi</label>
                                <input type="text" 
                                       name="questions[{{ $qIndex }}][text]" 
                                       placeholder="Nhập nội dung câu hỏi..."
                                       value="{{ old('questions.'.$qIndex.'.text', $question->question) }}"
                                       required>
                                <input type="hidden" 
                                       name="questions[{{ $qIndex }}][id]" 
                                       value="{{ $question->id }}">
                            </div>

                            <div class="options-grid">
                                @foreach($question->options as $oIndex => $opt)
                                    <div class="option-item {{ $opt->is_correct ? 'correct' : '' }}">
                                        <div class="option-input">
                                            <input type="text"
                                                   name="questions[{{ $qIndex }}][options][{{ $oIndex }}][text]"
                                                   placeholder="Đáp án {{ $oIndex + 1 }}"
                                                   value="{{ old('questions.'.$qIndex.'.options.'.$oIndex.'.text', $opt->option_text) }}"
                                                   required>
                                            <input type="hidden"
                                                   name="questions[{{ $qIndex }}][options][{{ $oIndex }}][id]"
                                                   value="{{ $opt->id }}">
                                        </div>
                                        <label class="correct-radio">
                                            <input type="radio" 
                                                   name="questions[{{ $qIndex }}][correct]" 
                                                   value="{{ $oIndex }}"
                                                   {{ old('questions.'.$qIndex.'.correct', $opt->is_correct ? $oIndex : '') == $oIndex ? 'checked' : '' }}
                                                   onchange="highlightCorrectOption(this)">
                                            <span>Đáp án đúng</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="help-text" style="margin-top: 15px;">
                                <i class="bi bi-lightbulb"></i> Chọn một đáp án đúng bằng cách tích vào radio button
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Add Question Button -->
                <div class="add-question">
                    <button type="button" class="btn-add-question" onclick="addQuestion()">
                        <i class="bi bi-plus-circle"></i> Thêm câu hỏi mới
                    </button>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-save"></i> Cập nhật Quiz
                    </button>
                    <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="btn-preview">
                        <i class="bi bi-eye"></i> Xem chi tiết
                    </a>
                
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Breadcrumb */
        .breadcrumb {
            color: #6c757d;
            font-size: 14px;
        }
        
        .breadcrumb a {
            color: #4776E6;
            transition: color 0.3s ease;
        }
        
        .breadcrumb a:hover {
            color: #8E54E9;
        }

        /* Form Container */
        .form-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            animation: slideUp 0.5s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }

        /* Progress Bar */
        .form-progress {
            background: linear-gradient(135deg, #f8fafd 0%, #f1f4f8 100%);
            padding: 20px 30px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .progress-steps {
            display: flex;
            gap: 30px;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #6c757d;
            font-size: 14px;
        }

        .step.active {
            color: #4776E6;
            font-weight: 500;
        }

        .step-number {
            width: 30px;
            height: 30px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .step.active .step-number {
            background: linear-gradient(135deg, #4776E6, #8E54E9);
            color: white;
        }

        /* Form Content */
        .form-content {
            padding: 40px;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 15px;
        }

        .form-group label i {
            color: #4776E6;
            margin-right: 8px;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
            color: #333;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #4776E6;
            box-shadow: 0 0 0 4px rgba(71, 118, 230, 0.1);
            outline: none;
        }

        /* Section Divider */
        .section-divider {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 40px 0 30px;
        }

        .section-divider hr {
            flex: 1;
            border: none;
            border-top: 2px dashed #e0e0e0;
        }

        .section-divider h3 {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 600;
            background: white;
            padding: 0 15px;
            white-space: nowrap;
        }

        .section-divider i {
            color: #4776E6;
        }

        /* Question Card */
        .question-card {
            background: #f8fafd;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            position: relative;
        }

        .question-card:hover {
            border-color: #4776E6;
            box-shadow: 0 10px 30px rgba(71, 118, 230, 0.1);
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-right: 40px;
        }

        .question-header h4 {
            color: #2c3e50;
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .question-badge {
            background: linear-gradient(135deg, #4776E6, #8E54E9);
            color: white;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 500;
        }

        .remove-question {
            background: #fee;
            color: #dc3545;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .remove-question:hover {
            background: #dc3545;
            color: white;
            transform: scale(1.1);
        }

        /* Options Grid */
        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .option-item {
            background: white;
            border-radius: 12px;
            padding: 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.2s ease;
        }

        .option-item:hover {
            border-color: #4776E6;
        }

        .option-item.correct {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .option-input {
            margin-bottom: 12px;
        }

        .option-input input[type="text"] {
            padding: 10px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }

        .correct-radio {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 13px;
            color: #2c3e50;
            cursor: pointer;
        }

        .correct-radio input[type="radio"] {
            width: 16px;
            height: 16px;
            accent-color: #10b981;
            cursor: pointer;
        }

        .correct-radio input[type="radio"]:checked + span {
            color: #10b981;
            font-weight: 500;
        }

        /* Add Question Button */
        .add-question {
            text-align: center;
            margin: 30px 0;
        }

        .btn-add-question {
            background: white;
            border: 2px dashed #4776E6;
            color: #4776E6;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-add-question:hover {
            background: linear-gradient(135deg, #4776E6, #8E54E9);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(71, 118, 230, 0.3);
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
        }

        .btn-submit {
            background: linear-gradient(135deg, #4776E6, #8E54E9);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(71, 118, 230, 0.3);
            text-decoration: none;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(71, 118, 230, 0.4);
            color: white;
        }

        .btn-preview {
            background: white;
            border: 2px solid #e0e0e0;
            color: #2c3e50;
            padding: 14px 30px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-preview:hover {
            border-color: #4776E6;
            color: #4776E6;
            background: #f8fafd;
        }

        .btn-cancel {
            background: white;
            border: 2px solid #e0e0e0;
            color: #dc3545;
            padding: 14px 30px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-cancel:hover {
            border-color: #dc3545;
            color: #dc3545;
            background: #fff5f5;
        }

        /* Help Text */
        .help-text {
            color: #6c757d;
            font-size: 13px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .help-text i {
            color: #4776E6;
        }

        /* Required field */
        .required::after {
            content: '*';
            color: #dc3545;
            margin-left: 4px;
        }

        /* Animation */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-content {
                padding: 30px 20px;
            }

            .options-grid {
                grid-template-columns: 1fr;
            }

            .progress-steps {
                flex-direction: column;
                gap: 15px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-submit,
            .btn-preview,
            .btn-cancel {
                width: 100%;
            }

            .section-divider h3 {
                font-size: 18px;
                white-space: normal;
            }
        }
    </style>

    <script>
        let questionCount = {{ count($quiz->questions) }};

        function addQuestion() {
            const container = document.getElementById('questions-container');
            const template = `
                <div class="question-card" id="question-${questionCount}">
                    <button type="button" class="remove-question" onclick="removeQuestion(${questionCount})">
                        <i class="bi bi-x"></i>
                    </button>
                    <div class="question-header">
                        <h4>Câu hỏi mới ${questionCount + 1}</h4>
                        <span class="question-badge">Mới</span>
                    </div>
                    
                    <div class="form-group">
                        <label>Nội dung câu hỏi</label>
                        <input type="text" 
                               name="new_questions[${questionCount}][text]" 
                               placeholder="Nhập nội dung câu hỏi..."
                               required>
                    </div>

                    <div class="options-grid">
                        ${[0,1,2,3].map(j => `
                            <div class="option-item">
                                <div class="option-input">
                                    <input type="text"
                                           name="new_questions[${questionCount}][options][${j}][text]"
                                           placeholder="Đáp án ${j+1}"
                                           required>
                                </div>
                                <label class="correct-radio">
                                    <input type="radio" 
                                           name="new_questions[${questionCount}][correct]" 
                                           value="${j}"
                                           onchange="highlightCorrectOption(this)">
                                    <span>Đáp án đúng</span>
                                </label>
                            </div>
                        `).join('')}
                    </div>

                    <div class="help-text" style="margin-top: 15px;">
                        <i class="bi bi-lightbulb"></i> Chọn một đáp án đúng bằng cách tích vào radio button
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', template);
            questionCount++;
        }

        function removeQuestion(id) {
            const question = document.getElementById(`question-${id}`);
            if (question && confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
                question.remove();
                updateQuestionNumbers();
            }
        }

        function updateQuestionNumbers() {
            const questions = document.querySelectorAll('.question-card');
            questions.forEach((question, index) => {
                question.id = `question-${index}`;
                const title = question.querySelector('h4');
                if (title) {
                    title.textContent = `Câu hỏi ${index + 1}`;
                }
            });
        }

        function highlightCorrectOption(radio) {
            const questionCard = radio.closest('.question-card');
            questionCard.querySelectorAll('.option-item').forEach(opt => {
                opt.classList.remove('correct');
            });
            
            const optionItem = radio.closest('.option-item');
            if (optionItem) {
                optionItem.classList.add('correct');
            }
        }

        // Khởi tạo sự kiện cho các radio button có sẵn
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    highlightCorrectOption(this);
                });
                
                // Highlight nếu đã được chọn
                if (radio.checked) {
                    highlightCorrectOption(radio);
                }
            });
        });
    </script>
@endsection