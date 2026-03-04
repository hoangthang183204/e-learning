<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            text-align: center;
            font-family: DejaVu Sans, sans-serif;
            border: 10px solid #000;
            padding: 50px;
        }

        h1 {
            font-size: 40px;
        }

        .name {
            font-size: 30px;
            margin: 20px 0;
            font-weight: bold;
        }

        .course {
            font-size: 24px;
            margin: 20px 0;
        }

        .date {
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <h1>🎓 CERTIFICATE OF COMPLETION</h1>

    <p>This certifies that</p>

    <div class="name">{{ $user->name }}</div>

    <p>has successfully completed the course</p>

    <div class="course">{{ $course->title }}</div>

    <div class="date">
        Date: {{ $date }}
    </div>

</body>

</html>
