<h2>📊 Dashboard - {{ $course->title }}</h2>

<hr>

<ul>
    <li><b>Số học viên:</b> {{ $totalStudents }}</li>
    <li><b>Pass rate:</b> {{ $passRate }}%</li>
    <li><b>Điểm trung bình:</b> {{ round($averageScore, 2) }}</li>
</ul>

<hr>

<h4>🏆 Top học viên</h4>

<table border="1" cellpadding="8">
    <tr>
        <th>#</th>
        <th>Tên</th>
        <th>Email</th>
        <th>Điểm TB</th>
    </tr>

    @foreach($topStudents as $index => $row)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $row->user->name }}</td>
            <td>{{ $row->user->email }}</td>
            <td>{{ round($row->avg_score, 2) }}</td>
        </tr>
    @endforeach
</table>

<br>

<a href="{{ route('teacher.dashboard') }}">← Quay lại dashboard</a>