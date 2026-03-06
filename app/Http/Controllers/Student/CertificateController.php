<?php

namespace App\Http\Controllers\Student; // Sửa namespace thành Student

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Hiển thị danh sách chứng chỉ của học viên
     */
    public function index()
    {
        $user = Auth::user();

        // Lấy tất cả khóa học đã hoàn thành
        $completedCourses = $user->courses()
            ->wherePivot('status', 'finished')
            ->wherePivot('completed_at', '!=', null)
            ->get();

        // Lấy chứng chỉ đã có
        $certificates = Certificate::where('user_id', $user->id)
            ->with('course')
            ->get()
            ->keyBy('course_id');

        return view('student.certificates.index', compact('completedCourses', 'certificates'));
    }

    /**
     * Tải chứng chỉ cho khóa học đã hoàn thành
     */
    public function download(Course $course)
    {
        $user = auth()->user();

        // Kiểm tra xem user đã hoàn thành khóa học chưa
        $enrollment = $user->courses()
            ->where('course_id', $course->id)
            ->wherePivot('status', 'finished')
            ->wherePivot('completed_at', '!=', null)
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Bạn chưa hoàn thành khóa học này');
        }

        // Kiểm tra hoặc tạo chứng chỉ
        $certificate = Certificate::firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id
            ],
            [
                'certificate_number' => $this->generateCertificateNumber(),
                'student_name' => $user->name,
                'course_name' => $course->title,
                'completion_date' => $enrollment->pivot->completed_at,
                'issued_at' => now(),
                'is_verified' => true
            ]
        );

        $data = [
            'user' => $user,
            'course' => $course,
            'certificate' => $certificate,
            'date' => \Carbon\Carbon::parse($enrollment->pivot->completed_at)->format('d/m/Y'),
            'certificate_number' => $certificate->certificate_number
        ];

        $pdf = Pdf::loadView('student.certificates.template', $data);

        return $pdf->download('chung-chi-' . $course->slug . '.pdf');
    }

    /**
     * Xem chứng chỉ trực tuyến
     */
    /**
     * Xem chứng chỉ trực tuyến
     */
    public function show(Course $course)
    {
        $user = auth()->user(); // Lấy user hiện tại

        $enrollment = $user->courses()
            ->where('course_id', $course->id)
            ->wherePivot('status', 'finished')
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Bạn chưa hoàn thành khóa học này');
        }

        $certificate = Certificate::firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id
            ],
            [
                'certificate_number' => $this->generateCertificateNumber(),
                'student_name' => $user->name,
                'course_name' => $course->title,
                'completion_date' => $enrollment->pivot->completed_at,
                'issued_at' => now(),
                'is_verified' => true
            ]
        );

        // Truyền đầy đủ biến sang view
        return view('student.certificates.show', compact(
            'course',
            'certificate',
            'enrollment',
            'user'  // <-- THÊM BIẾN NÀY
        ));
    }

    /**
     * Xác thực chứng chỉ công khai
     */
    public function verify($certificateNumber)
    {
        $certificate = Certificate::where('certificate_number', $certificateNumber)
            ->with(['user', 'course'])
            ->first();

        if (!$certificate) {
            return view('student.certificates.verify', [
                'valid' => false,
                'number' => $certificateNumber
            ]);
        }

        return view('student.certificates.verify', [
            'valid' => true,
            'certificate' => $certificate
        ]);
    }

    /**
     * Tạo số chứng chỉ duy nhất
     */
    private function generateCertificateNumber()
    {
        $prefix = 'EL';
        $year = date('Y');
        $month = date('m');
        $random = strtoupper(substr(uniqid(), -6));

        return "{$prefix}-{$year}{$month}-{$random}";
    }
}
