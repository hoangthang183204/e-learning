@extends('student.layout')

@section('title', 'Chứng chỉ - ' . $course->title)
@section('page-icon', 'award')
@section('page-title', 'Chứng chỉ hoàn thành khóa học')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('student.certificates.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
                <a href="{{ route('student.certificates.download', $course) }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Tải chứng chỉ PDF
                </a>
            </div>
        </div>

        <div class="col-12">
            <div class="certificate-wrapper p-4 bg-light rounded">
                <div class="certificate-container"
                    style="background: white; padding: 40px; border: 20px solid #2c3e50; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div class="text-center">
                        <!-- Header -->
                        <div class="mb-5">
                            <h1
                                style="font-size: 48px; color: #2c3e50; margin: 0; text-transform: uppercase; letter-spacing: 5px; font-weight: bold;">
                                CHỨNG CHỈ
                            </h1>
                            <h2 style="font-size: 24px; color: #3498db; margin: 10px 0 0; font-weight: normal;">
                                HOÀN THÀNH KHÓA HỌC
                            </h2>
                        </div>

                        <!-- Nội dung -->
                        <div class="my-5">
                            <p style="font-size: 18px; color: #7f8c8d; margin-bottom: 20px;">Chứng nhận</p>

                            <h3
                                style="font-size: 42px; font-weight: bold; color: #2c3e50; margin: 20px 0; padding: 20px; border-top: 2px dashed #3498db; border-bottom: 2px dashed #3498db; display: inline-block;">
                                {{ $user->name }}
                            </h3>

                            <p style="font-size: 20px; color: #34495e; margin: 30px 0;">đã hoàn thành khóa học</p>

                            <h4 style="font-size: 32px; font-weight: bold; color: #3498db; margin: 15px 0;">
                                {{ $course->title }}
                            </h4>
                        </div>

                        <!-- Thông tin thêm -->
                        <div class="mt-5">
                            <p style="font-size: 18px; color: #7f8c8d; margin: 10px 0;">
                                Ngày cấp: {{ \Carbon\Carbon::parse($enrollment->pivot->completed_at)->format('d/m/Y') }}
                            </p>
                            <p style="font-size: 14px; color: #95a5a6;">
                                Số hiệu: {{ $certificate->certificate_number }}
                            </p>
                        </div>

                        <!-- Chữ ký -->
                        <div class="mt-5" style="display: flex; justify-content: space-around;">
                            <div style="text-align: center;">
                                <div style="width: 200px; height: 1px; background: #2c3e50; margin-bottom: 10px;"></div>
                                <div>Giảng viên</div>
                            </div>
                            <div style="text-align: center;">
                                <div style="width: 200px; height: 1px; background: #2c3e50; margin-bottom: 10px;"></div>
                                <div>Ban đào tạo</div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-5" style="font-size: 12px; color: #95a5a6;">
                            Chứng chỉ có giá trị xác thực tại website e-learning.app
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .certificate-wrapper {
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .certificate-container {
            max-width: 1000px;
            margin: 0 auto;
            position: relative;
        }

        .certificate-container::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #3498db;
            pointer-events: none;
        }
    </style>
@endsection
