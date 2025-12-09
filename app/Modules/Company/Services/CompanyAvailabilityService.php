<?php
namespace App\Modules\Company\Services;

use App\Modules\Company\Repositories\CompanyAvailabilityRepositoryInterface;
use App\Models\Appointment;

class CompanyAvailabilityService
{
    public function __construct(private CompanyAvailabilityRepositoryInterface $repo) {}

    // Upsert one (update if id exists, create if not) + prevent overlap
    public function upsertForCompany(int $companyId, array $data)
    {
        $data['company_id'] = $companyId;

        // Prevent overlapping availability for same company on same day/day of week
        $this->assertNoOverlap($companyId, $data);

        if (!empty($data['id'])) return $this->repo->update($data['id'], $data);
        return $this->repo->create($data);
    }

    private function assertNoOverlap(int $companyId, array $data): void
    {
        $query = \App\Models\CompanyAvailability::where('company_id', $companyId)
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

    // Free slots for display to clients
    public function generateFreeSlots(int $companyId, string $date, int $slotMinutes = 30): array
    {
        $availabilities = $this->repo->getForDate($companyId, $date);
        $booked = Appointment::where('company_id', $companyId)
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

