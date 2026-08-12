<?php

namespace App\Services;

use App\Models\ParentModel;
use App\Models\ParentQrCode;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ParentQrCodeService
{
    public function resolve(string $code): ?ParentModel
    {
        $code = trim($code);
        if ($code === '') {
            return null;
        }

        $qr = ParentQrCode::query()
            ->active()
            ->with('parent.user')
            ->where('code', $code)
            ->first();

        if ($qr?->parent) {
            return $qr->parent;
        }

        // Compatibility fallback for installations where the alias backfill
        // has not run yet.
        return ParentModel::with('user')->where('qr_code_string', $code)->first();
    }

    public function syncForParent(ParentModel $parent): Collection
    {
        if (! $parent->exists) {
            return collect();
        }

        $parent->loadMissing('students');

        if (blank($parent->qr_code_string)) {
            $parent->forceFill(['qr_code_string' => $this->generateCanonicalCode($parent)])->saveQuietly();
        }

        $this->ensureCanonical($parent);

        foreach ($parent->students as $student) {
            $this->ensureChildAlias($parent, $student);
        }

        return $this->codesForParent($parent);
    }

    public function syncAll(): int
    {
        $count = 0;

        ParentModel::with('students')->orderBy('id')->chunkById(200, function ($parents) use (&$count) {
            foreach ($parents as $parent) {
                $this->syncForParent($parent);
                $count++;
            }
        });

        return $count;
    }

    public function ensureChildAlias(ParentModel $parent, Student $student): ParentQrCode
    {
        $prefix = $this->prefixFor($parent);
        $base = Str::limit($prefix.$student->nis, 90, '');
        $candidate = $base;
        $counter = 1;

        while (($existing = ParentQrCode::where('code', $candidate)->first()) && $existing->parent_id !== $parent->id) {
            $suffix = '-'.$parent->id.'-'.$counter++;
            $candidate = Str::limit($base, 100 - strlen($suffix), '').$suffix;
        }

        $existing = ParentQrCode::where('code', $candidate)->first();
        if ($existing) {
            if ($existing->parent_id === $parent->id && $existing->source_student_id !== $student->id) {
                $existing->update(['source_student_id' => $student->id, 'is_active' => true, 'revoked_at' => null]);
            }

            return $existing;
        }

        return ParentQrCode::create([
            'parent_id' => $parent->id,
            'code' => $candidate,
            'kind' => 'child_alias',
            'source_student_id' => $student->id,
            'is_active' => true,
        ]);
    }

    public function codesForParent(ParentModel $parent): Collection
    {
        return ParentQrCode::query()
            ->active()
            ->with('sourceStudent')
            ->where('parent_id', $parent->id)
            ->orderByRaw("CASE WHEN kind = 'canonical' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->get();
    }

    public function matchedCodeDetails(string $code): ?array
    {
        $code = trim($code);
        $qr = ParentQrCode::query()->active()->where('code', $code)->first();
        $parent = $qr?->parent ?? ParentModel::where('qr_code_string', $code)->first();

        if (! $parent) {
            return null;
        }

        return [
            'parent' => $parent,
            'matched_qr_code' => $code,
            'canonical_qr_code' => $parent->qr_code_string,
            'qr_code_source' => $qr?->kind ?? 'legacy_column',
        ];
    }

    private function ensureCanonical(ParentModel $parent): ParentQrCode
    {
        return ParentQrCode::firstOrCreate(
            ['code' => $parent->qr_code_string],
            [
                'parent_id' => $parent->id,
                'kind' => 'canonical',
                'is_active' => true,
            ]
        );
    }

    private function generateCanonicalCode(ParentModel $parent): string
    {
        do {
            $code = 'P-'.$parent->id.'-'.Str::upper(Str::random(8));
        } while (ParentQrCode::where('code', $code)->exists() || ParentModel::where('qr_code_string', $code)->exists());

        return $code;
    }

    private function prefixFor(ParentModel $parent): string
    {
        return match ($parent->type) {
            'father' => 'A',
            'mother' => 'B',
            'teacher' => 'T',
            default => 'X',
        };
    }
}
