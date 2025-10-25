<?php
namespace App\Modules\Favorites\Services;

use App\Modules\Favorites\Repositories\FavoriteRepositoryInterface;
use App\Models\Lawyer;
use App\Models\Company;
use RuntimeException;

class FavoriteService
{
    public function __construct(private FavoriteRepositoryInterface $repo) {}

    /** نحسم أي نصّ ('lawyer'|'company') إلى كلاس الموديل */
    private function resolveModel(string $type): string
    {
        return match(strtolower($type)) {
            'lawyer'  => Lawyer::class,
            'company' => Company::class,
            default   => throw new RuntimeException('Invalid target type.'),
        };
    }

    /**
     * تخزين النوع كنص ('lawyer'|'company') وليس اسم كلاس.
     * التحقق من الوجود يتم بالكلاس.
     */
    public function toggle(int $userId, string $targetType, int $targetId): array
    {
        $type  = strtolower($targetType);      // يُخزَّن في DB
        $model = $this->resolveModel($type);   // للتحقق فقط

        // تأكيد وجود الهدف
        if (! $model::query()->whereKey($targetId)->exists()) {
            throw new RuntimeException('Target not found.');
        }

        // Idempotent toggle
        if ($this->repo->exists($userId, $type, $targetId)) { // <-- لاحظ: نمرّر النص مش الكلاس
            $this->repo->remove($userId, $type, $targetId);
            return ['favorited' => false];
        }

        $this->repo->add($userId, $type, $targetId);
        return ['favorited' => true];
    }

    /** لو حابب دوالّ متخصصة */
    public function toggleLawyer(int $userId, int $lawyerId): array
    {
        return $this->toggle($userId, 'lawyer', $lawyerId);
    }
}
