<h3>Kết quả quiz - {{ $course->title }}</h3>

<table border="1" cellpadding="8">
<tr>
    <th>Học viên</th>
    <th>Quiz</th>
    <th>Điểm</th>
    <th>Trạng thái</th>
</tr>

@foreach ($results as $r)
<tr>
    <td>{{ $r->user->name }}</td>
    <td>{{ $r->quiz->title }}</td>
    <td>{{ $r->score }}</td>
    <td>{{ $r->passed ? 'ĐẠT' : 'TRƯỢT' }}</td>
</tr>
@endforeach
</table>