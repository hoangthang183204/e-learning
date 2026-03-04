<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function download(Course $course)
    {
        $user = auth()->user();

        $enrollment = $user->courses()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment || !$enrollment->pivot->completed_at) {
            abort(403, 'Bạn chưa hoàn thành khoá học');
        }

        $data = [
            'user' => $user,
            'course' => $course,
            'date' => \Carbon\Carbon::parse(
                $enrollment->pivot->completed_at
            )->format('d/m/Y')
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificate.template', $data);

        return $pdf->download('certificate-' . $course->id . '.pdf');
    }
}
