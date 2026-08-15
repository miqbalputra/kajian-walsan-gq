<?php

namespace App\Services;

use App\Models\ParentModel;
use App\Models\User;
use App\Models\UserLoginAlias;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParentLoginAliasService
{
    /**
     * Add a BPK/IBU login for every child without replacing the parent's
     * canonical username or password. Existing aliases are left untouched so
     * a parent changing their password is not silently undone by a resync.
     *
     * @return array<int, array{username: string, password: string, created: bool, student_id: int}>
     */
    public function syncForParent(ParentModel $parent): array
    {
        if (! $parent->user || ! $parent->isGuardian()) {
            return [];
        }

        $parent->loadMissing('students');
        $credentials = [];

        foreach ($parent->students as $student) {
            if (blank($student->nis)) {
                continue;
            }

            $username = $this->usernameFor($parent, (string) $student->nis);

            // A canonical username already belongs to this user; its normal
            // user password remains the source of truth.
            if ($parent->user->username === $username) {
                continue;
            }

            $existingUser = User::whereRaw('LOWER(username) = ?', [Str::lower($username)])->first();
            if ($existingUser && $existingUser->id !== $parent->user_id) {
                throw new \RuntimeException("Username login {$username} sudah dipakai akun lain.");
            }

            $alias = UserLoginAlias::whereRaw('LOWER(username) = ?', [Str::lower($username)])->first();
            if ($alias && $alias->user_id !== $parent->user_id) {
                throw new \RuntimeException("Alias login {$username} sudah dipakai akun lain.");
            }

            $created = ! $alias;
            if ($alias) {
                $alias->update([
                    'source_student_id' => $student->id,
                    'is_active' => true,
                    'revoked_at' => null,
                ]);
            } else {
                // On first creation the requested child credential is
                // username=password. If the parent later changes password,
                // the password update actions synchronize this hash.
                $alias = UserLoginAlias::create([
                    'user_id' => $parent->user_id,
                    'source_student_id' => $student->id,
                    'username' => $username,
                    'password' => $username,
                    'kind' => 'child_alias',
                    'is_active' => true,
                ]);
            }

            $credentials[] = [
                'username' => $username,
                'password' => $created ? $username : null,
                'created' => $created,
                'student_id' => $student->id,
            ];
        }

        return $credentials;
    }

    public function usernameFor(ParentModel $parent, string $nis): string
    {
        $prefix = match ($parent->type) {
            'father' => 'BPK',
            'mother' => 'IBU',
            default => 'WALI',
        };

        return Str::upper($prefix).trim($nis);
    }

    public function syncPassword(User $user, string $password): void
    {
        foreach ($user->loginAliases()->get() as $alias) {
            $alias->forceFill(['password' => Hash::make($password)])->saveQuietly();
        }
    }
}
