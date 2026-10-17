<?php

namespace App\Auth;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

class MultiTableUserProvider implements UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     */
    public function retrieveById($identifier)
    {
        if (empty($identifier)) {
            return null;
        }

        // 1. Cek prefix jika ada (backward-compatible)
        if (str_starts_with((string) $identifier, 'siswa:')) {
            return Siswa::find(substr($identifier, 6));
        }
        if (str_starts_with((string) $identifier, 'petugas:')) {
            return User::find(substr($identifier, 8));
        }

        // 2. Cek session auth_model_type
        $modelType = session('auth_model_type');
        if ($modelType === 'siswa') {
            return Siswa::find($identifier) ?? User::find($identifier);
        }
        if ($modelType === 'petugas') {
            return User::find($identifier) ?? Siswa::find($identifier);
        }

        // 3. Cek rute URL saat ini
        if (request()->is('murid*')) {
            return Siswa::find($identifier) ?? User::find($identifier);
        }
        if (request()->is('admin*') || request()->is('panitia*')) {
            return User::find($identifier) ?? Siswa::find($identifier);
        }

        // 4. Fallback default
        return User::find($identifier) ?? Siswa::find($identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     */
    public function retrieveByToken($identifier, $token)
    {
        $user = $this->retrieveById($identifier);
        if (! $user) {
            return null;
        }

        $rememberToken = $user->getRememberToken();
        return $rememberToken && hash_equals($rememberToken, $token) ? $user : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $user->save();
    }

    /**
     * Retrieve a user by the given credentials.
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) || (count($credentials) === 1 && array_key_exists('password', $credentials))) {
            return null;
        }

        $identity = $credentials['login_identity'] ?? $credentials['email'] ?? $credentials['username'] ?? null;
        if (! $identity) {
            return null;
        }

        // 1. Cek pada tabel petugas (Administrator & Panitia SPMB)
        $petugas = User::where('email', $identity)
            ->orWhere('username', $identity)
            ->orWhere('phone', $identity)
            ->first();

        if ($petugas) {
            if (session()->isStarted()) {
                session(['auth_model_type' => 'petugas']);
            }
            return $petugas;
        }

        // 2. Cek pada tabel siswa (Calon Siswa Baru)
        $siswa = Siswa::where('email', $identity)
            ->orWhere('nisn', $identity)
            ->orWhere('phone', $identity)
            ->first();

        if ($siswa) {
            if (session()->isStarted()) {
                session(['auth_model_type' => 'siswa']);
            }
            return $siswa;
        }

        return null;
    }

    /**
     * Validate a user against the given credentials.
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];
        return Hash::check($plain, $user->getAuthPassword());
    }

    /**
     * Rehash the user's password if required and supported.
     */
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        // Rehash jika diperlukan
    }
}
