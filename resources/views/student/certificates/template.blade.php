<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chứng chỉ hoàn thành khóa học</title>
    <style>
        @page {
            margin: 0;
            size: landscape;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
        }
        .certificate {
            width: 1000px;
            height: 700px;
            margin: 0 auto;
            background: white;
            border: 20px solid #2c3e50;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .border-pattern {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #3498db;
            pointer-events: none;
        }
        .content {
            position: relative;
            padding: 60px 40px;
            text-align: center;
            z-index: 2;
        }
        .header {
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 48px;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 5px;
            font-weight: bold;
        }
        .header h2 {
            font-size: 24px;
            color: #3498db;
            margin: 10px 0 0;
            font-weight: normal;
            text-transform: uppercase;
        }
        .seal {
            position: absolute;
            top: 50px;
            right: 80px;
            width: 100px;
            height: 100px;
            border: 3px solid #f1c40f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-15deg);
            opacity: 0.8;
        }
        .seal span {
            font-size: 14px;
            font-weight: bold;
            color: #f1c40f;
            text-transform: uppercase;
            transform: rotate(15deg);
        }
        .presented {
            font-size: 18px;
            color: #7f8c8d;
            margin: 40px 0 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .student-name {
            font-size: 42px;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0;
            padding: 20px;
            border-top: 2px dashed #3498db;
            border-bottom: 2px dashed #3498db;
            display: inline-block;
        }
        .course-info {
            font-size: 24px;
            color: #34495e;
            margin: 30px 0;
        }
        .course-title {
            font-size: 32px;
            font-weight: bold;
            color: #3498db;
            margin: 15px 0;
        }
        .date {
            font-size: 20px;
            color: #7f8c8d;
            margin: 40px 0 20px;
        }
        .certificate-number {
            font-size: 14px;
            color: #95a5a6;
            margin-top: 30px;
        }
        .signature {
            margin-top: 60px;
            display: flex;
            justify-content: space-around;
        }
        .signature-item {
            text-align: center;
        }
        .signature-line {
            width: 200px;
            height: 1px;
            background: #2c3e50;
            margin-bottom: 10px;
        }
        .footer {
            position: absolute;
            bottom: 30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            color: #95a5a6;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="border-pattern"></div>
        <div class="content">
            <div class="header">
                <h1>CHỨNG CHỈ</h1>
                <h2>HOÀN THÀNH KHÓA HỌC</h2>
            </div>
            
            <div class="seal">
                <span>E-LEARNING</span>
            </div>

            <div class="presented">Chứng nhận</div>
            
            <div class="student-name">{{ $user->name }}</div>
            
            <div class="course-info">đã hoàn thành khóa học</div>
            
            <div class="course-title">{{ $course->title }}</div>
            
            <div class="date">
                Ngày cấp: {{ $date }}
            </div>
            
            @if(isset($certificate_number))
            <div class="certificate-number">
                Số hiệu: {{ $certificate_number }}
            </div>
            @endif

            <div class="signature">
                <div class="signature-item">
                    <div class="signature-line"></div>
                    <div>Giảng viên</div>
                </div>
                <div class="signature-item">
                    <div class="signature-line"></div>
                    <div>Ban đào tạo</div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            Chứng chỉ có giá trị xác thực tại website e-learning.app
        </div>
    </div>
</body>
</html>