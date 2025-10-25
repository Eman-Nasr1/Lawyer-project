<?php
namespace App\Modules\Lawyer\Services;

use App\Modules\Lawyer\Repositories\LawyerAvailabilityRepositoryInterface;
use App\Models\Appointment;

class LawyerAvailabilityService
{
    public function __construct(private LawyerAvailabilityRepositoryInterface $repo) {}

    // Upsert واحد (لو فيه id يعدّل، لو مفيش ينشئ) + منع تداخل
    public function upsertForLawyer(int $lawyerId, array $data)
    {
        $data['lawyer_id'] = $lawyerId;

        // منع تداخل التوافر لنفس المحامي في نفس اليوم/اليوم الأسبوعي
        $this->assertNoOverlap($lawyerId, $data);

        if (!empty($data['id'])) return $this->repo->update($data['id'], $data);
        return $this->repo->create($data);
    }

    private function assertNoOverlap(int $lawyerId, array $data): void
    {
        $query = \App\Models\LawyerAvailability::where('lawyer_id', $lawyerId)
            ->where('is_active', true);

        if (!empty($data['id'])) $query->where('id', '!=', $data['id']);

        if (!empty($data['date'])) {
            $query->where('date', $data['date']);
        } elseif (!empty($data['day_of_week'])) {
            $query->where('day_of_week', $data['day_of_week']);
        }

        $exist = $query->get();
        foreach ($exist as $a) {
            if ($this->overlaps($data['start_time'], $data['end_time'], $a->start_time, $a->end_time)) {
                throw new \RuntimeException('Overlapping availability time range.');
            }
        }
    }

    private function overlaps(string $s1, string $e1, string $s2, string $e2): bool
    {   // !(end1 <= start2 || start1 >= end2)
        return !($e1 <= $s2 || $s1 >= $e2);
    }

    // سلات فاضية للعرض على العميل
    public function generateFreeSlots(int $lawyerId, string $date, int $slotMinutes = 30): array
    {
        $availabilities = $this->repo->getForDate($lawyerId, $date);
        $booked = Appointment::where('lawyer_id', $lawyerId)
            ->whereDate('date', $date)
            ->whereIn('status', ['pending','confirmed'])
            ->get(['start_time','end_time']);

        $bookedRanges = $booked->map(fn($a)=>[$a->start_time, $a->end_time])->toArray();

        $slots = [];
        foreach ($availabilities as $a) {
            $slots = array_merge($slots, $this->sliceToSlots($date, $a->start_time, $a->end_time, $slotMinutes, $bookedRanges));
        }
        return $slots;
    }

    private function sliceToSlots(string $date, string $start, string $end, int $mins, array $booked): array
    {
        $cursor = strtotime("$date $start");
        $endTs  = strtotime("$date $end");
        $out = [];

        while ($cursor + ($mins*60) <= $endTs) {
            $s = date('H:i', $cursor);
            $e = date('H:i', $cursor + $mins*60);
            if (!$this->overlapsAny($s, $e, $booked)) $out[] = ['start'=>$s,'end'=>$e];
            $cursor += $mins*60;
        }
        return $out;
    }

    private function overlapsAny(string $s, string $e, array $ranges): bool
    {
        foreach ($ranges as [$bs, $be]) {
            if (!($e <= $bs || $s >= $be)) return true;
        }
        return false;
    }
}
