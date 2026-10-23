<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class M365AuthController extends Controller
{
    /**
     * Redirect user directly to Microsoft 365 OAuth 2.0 endpoint.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $clientId = config('services.m365.client_id');
        $tenantId = config('services.m365.tenant_id', 'common');
        $redirectUri = config('services.m365.redirect_uri') ?: route('auth.m365.callback');

        $state = Str::random(40);
        $request->session()->put('m365_oauth_state', $state);

        $params = http_build_query([
            'client_id' => (string) $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'response_mode' => 'query',
            'scope' => 'openid profile email User.Read',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return redirect()->away("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/authorize?{$params}");
    }

    /**
     * Handle OAuth 2.0 callback from Microsoft Entra ID.
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            $errorDesc = $request->input('error_description', $request->input('error'));

            return redirect()->route('login')->withErrors([
                'email' => "Log masuk Microsoft 365 dibatalkan atau gagal: {$errorDesc}",
            ]);
        }

        $expectedState = $request->session()->pull('m365_oauth_state');
        $returnedState = (string) $request->input('state');

        if (empty($expectedState) || ! hash_equals($expectedState, $returnedState)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi keselamatan (OAuth state) tamat tempoh atau tidak sah. Sila cuba log masuk semula.',
            ]);
        }

        $code = (string) $request->input('code');
        if (empty($code)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Kod kebenaran Microsoft 365 tidak ditemui.',
            ]);
        }

        $clientId = config('services.m365.client_id');
        $clientSecret = config('services.m365.client_secret');
        $tenantId = config('services.m365.tenant_id', 'common');
        $redirectUri = config('services.m365.redirect_uri') ?: route('auth.m365.callback');

        // Exchange authorization code for access token
        $tokenResponse = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'scope' => 'openid profile email User.Read',
        ]);

        if (! $tokenResponse->successful()) {
            $error = $tokenResponse->json('error_description') ?? 'Pertukaran token kebenaran Microsoft gagal.';

            return redirect()->route('login')->withErrors([
                'email' => "Ralat pengesahan Microsoft 365: {$error}",
            ]);
        }

        $accessToken = $tokenResponse->json('access_token');

        // Retrieve authenticated user profile from Microsoft Graph API
        $graphResponse = Http::withToken($accessToken)
            ->get('https://graph.microsoft.com/v1.0/me?$select=id,displayName,mail,userPrincipalName,jobTitle,department,mobilePhone');

        if (! $graphResponse->successful()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal memuat turun profil pengguna daripada Microsoft Graph API.',
            ]);
        }

        $profile = $graphResponse->json();

        return $this->handleM365Profile($request, $profile);
    }

    /**
     * Authenticate or auto-provision user based on verified Microsoft 365 profile data.
     */
    public function handleM365Profile(Request $request, array $profile): RedirectResponse
    {
        $rawEmail = $profile['mail'] ?? $profile['userPrincipalName'] ?? '';
        $email = strtolower(trim((string) $rawEmail));
        $m365Id = $profile['id'] ?? null;
        $name = trim((string) ($profile['displayName'] ?? 'Pengguna M365'));
        $department = trim((string) ($profile['department'] ?? 'Akademi Sains Malaysia'));
        $position = trim((string) ($profile['jobTitle'] ?? 'Pegawai'));
        $phone = trim((string) ($profile['mobilePhone'] ?? '03-8365 0000'));

        $allowedDomain = strtolower((string) config('services.m365.allowed_domain', 'akademisains.gov.my'));

        // Enforce strict domain checking for @akademisains.gov.my
        if (empty($email) || ! str_ends_with($email, '@'.$allowedDomain)) {
            AuditLog::record(
                'Cubaan Log Masuk M365 Ditolak',
                'Keselamatan',
                "Percubaan log masuk dengan emel tidak sah/luar domain: '{$email}'."
            );

            return redirect()->route('login')->withErrors([
                'email' => "Akses ditolak: Hanya pemegang akaun Microsoft 365 rasmi Akademi Sains Malaysia (@{$allowedDomain}) dibenarkan log masuk.",
            ]);
        }

        // Check if user exists by m365_id or email
        $user = null;
        if (! empty($m365Id)) {
            $user = User::where('m365_id', $m365Id)->first();
        }

        if (! $user) {
            $user = User::where('email', $email)->first();
        }

        if ($user) {
            // Existing user
            if (! $user->is_active) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akaun anda telah dinyahaktifkan. Sila hubungi Unit Pengurusan Fasiliti (UPF) untuk maklumat lanjut.',
                ]);
            }

            // Sync M365 ID if not already saved
            if (empty($user->m365_id) && ! empty($m365Id)) {
                $user->m365_id = $m365Id;
                $user->save();
            }
        } else {
            // Auto-provision new user as applicant (pemohon)
            $user = User::create([
                'name' => $name ?: 'Pemohon ASM',
                'email' => $email,
                'm365_id' => $m365Id,
                'password' => Hash::make(Str::random(32)),
                'role' => 'pemohon',
                'department' => $department ?: 'Akademi Sains Malaysia',
                'position' => $position ?: 'Pegawai',
                'phone' => $phone ?: '03-8365 0000',
                'is_active' => true,
            ]);

            AuditLog::record(
                'Pendaftaran Automatik M365',
                'Pengguna',
                "Akaun pemohon baharu telah didaftarkan secara automatik melalui M365 SSO: {$user->name} ({$user->email}).",
                $user
            );
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        AuditLog::record(
            'Log Masuk M365 Berjaya',
            'Keselamatan',
            "Pengguna {$user->name} ({$user->email}) berjaya log masuk melalui Microsoft 365 Single Sign-On.",
            $user
        );

        return redirect()->intended(route('dashboard'))->with(
            'success',
            "Selamat datang, {$user->name}! Log masuk menggunakan Microsoft 365 ASM berjaya."
        );
    }
}
