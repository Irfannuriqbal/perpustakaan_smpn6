<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Visit;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class VisitService
{
    public const DUPLICATE_WINDOW_MINUTES = 5;

    public function recordByMemberCode(string $memberCode): Visit
    {
        $student = Student::where('member_code', $memberCode)->first();

        if (! $student) {
            throw ValidationException::withMessages([
                'member_code' => 'Siswa dengan kode anggota tersebut tidak ditemukan.',
            ]);
        }

        if ($student->status !== 'aktif') {
            throw ValidationException::withMessages([
                'member_code' => 'Siswa tidak aktif dan tidak dapat dicatat kunjungannya.',
            ]);
        }

        $latestVisit = Visit::where('student_id', $student->id)
            ->whereDate('visit_date', now()->toDateString())
            ->latest('id')
            ->first();

        if ($latestVisit && $this->visitedTooRecently($latestVisit)) {
            throw ValidationException::withMessages([
                'member_code' => 'Kunjungan siswa ini sudah tercatat kurang dari '.self::DUPLICATE_WINDOW_MINUTES.' menit yang lalu.',
            ]);
        }

        return Visit::create([
            'student_id' => $student->id,
            'visit_date' => now()->toDateString(),
            'check_in_time' => now()->format('H:i'),
        ])->load('student');
    }

    private function visitedTooRecently(Visit $visit): bool
    {
        $visitedAt = Carbon::parse($visit->visit_date->format('Y-m-d').' '.$visit->check_in_time);

        return $visitedAt->diffInMinutes(now()) < self::DUPLICATE_WINDOW_MINUTES;
    }
}
