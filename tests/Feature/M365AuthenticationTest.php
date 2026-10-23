<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class M365AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_m365_sso_button(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Log Masuk dengan Microsoft 365');
        $response->assertSee(route('auth.m365.redirect'));
        $response->assertSee('@akademisains.gov.my');
    }

    public function test_m365_redirect_always_goes_directly_to_microsoft_oauth(): void
    {
        Config::set('services.m365.client_id', 'azure-asm-client-id');
        Config::set('services.m365.tenant_id', 'common');

        $response = $this->get(route('auth.m365.redirect'));

        $response->assertRedirect();
        $targetUrl = $response->headers->get('Location');

        $this->assertStringContainsString('login.microsoftonline.com/common/oauth2/v2.0/authorize', $targetUrl);
        $this->assertStringContainsString('client_id=azure-asm-client-id', $targetUrl);
        $this->assertStringContainsString('response_type=code', $targetUrl);
        $this->assertStringContainsString('scope=openid+profile+email+User.Read', $targetUrl);
        $this->assertNotNull(session('m365_oauth_state'));
    }

    public function test_m365_callback_successful_for_existing_user(): void
    {
        Config::set('services.m365.client_id', 'test-client-id');
        Config::set('services.m365.client_secret', 'test-client-secret');
        Config::set('services.m365.tenant_id', 'test-tenant');

        $user = User::create([
            'name' => 'Mohd Azim',
            'email' => 'mohd.azim@akademisains.gov.my',
            'role' => 'pemohon',
            'department' => 'Bahagian Dasar',
            'position' => 'Pegawai Sains',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        Http::fake([
            'https://login.microsoftonline.com/test-tenant/oauth2/v2.0/token' => Http::response([
                'access_token' => 'mock-access-token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://graph.microsoft.com/v1.0/me*' => Http::response([
                'id' => 'm365-azure-guid-001',
                'displayName' => 'Mohd Azim',
                'mail' => 'mohd.azim@akademisains.gov.my',
                'userPrincipalName' => 'mohd.azim@akademisains.gov.my',
                'department' => 'Bahagian Dasar',
                'jobTitle' => 'Pegawai Sains',
                'mobilePhone' => '012-3456789',
            ], 200),
        ]);

        $state = 'test-secure-oauth-state-token';
        session(['m365_oauth_state' => $state]);

        $response = $this->get(route('auth.m365.callback', [
            'code' => 'auth-code-12345',
            'state' => $state,
        ]));

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertSame($user->id, Auth::id());

        $user->refresh();
        $this->assertSame('m365-azure-guid-001', $user->m365_id);
    }

    public function test_m365_callback_auto_provisions_new_applicant_for_akademisains_domain(): void
    {
        Config::set('services.m365.client_id', 'test-client-id');
        Config::set('services.m365.client_secret', 'test-client-secret');
        Config::set('services.m365.tenant_id', 'common');

        $newEmail = 'dr.salmah@akademisains.gov.my';
        $this->assertDatabaseMissing('users', ['email' => $newEmail]);

        Http::fake([
            'https://login.microsoftonline.com/common/oauth2/v2.0/token' => Http::response([
                'access_token' => 'mock-access-token-new-user',
            ], 200),
            'https://graph.microsoft.com/v1.0/me*' => Http::response([
                'id' => 'm365-guid-salmah',
                'displayName' => 'Dr. Salmah Binti Hashim',
                'mail' => $newEmail,
                'userPrincipalName' => $newEmail,
                'department' => 'Bahagian Inovasi',
                'jobTitle' => 'Pengarah Bersekutu',
                'mobilePhone' => '019-8765432',
            ], 200),
        ]);

        $state = 'test-state-salmah';
        session(['m365_oauth_state' => $state]);

        $response = $this->get(route('auth.m365.callback', [
            'code' => 'auth-code-salmah',
            'state' => $state,
        ]));

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue(Auth::check());

        $this->assertDatabaseHas('users', [
            'email' => $newEmail,
            'name' => 'Dr. Salmah Binti Hashim',
            'role' => 'pemohon',
            'department' => 'Bahagian Inovasi',
            'position' => 'Pengarah Bersekutu',
            'is_active' => true,
        ]);

        $currentUser = Auth::user();
        $this->assertSame('pemohon', $currentUser->role);
    }

    public function test_m365_callback_rejects_non_akademisains_email_domain(): void
    {
        Config::set('services.m365.client_id', 'test-client-id');
        Config::set('services.m365.client_secret', 'test-client-secret');
        Config::set('services.m365.tenant_id', 'common');

        $unauthorizedEmail = 'pemohon.luar@gmail.com';

        Http::fake([
            'https://login.microsoftonline.com/common/oauth2/v2.0/token' => Http::response([
                'access_token' => 'mock-access-token-gmail',
            ], 200),
            'https://graph.microsoft.com/v1.0/me*' => Http::response([
                'id' => 'm365-guid-gmail',
                'displayName' => 'Penceroboh Luar',
                'mail' => $unauthorizedEmail,
                'userPrincipalName' => $unauthorizedEmail,
            ], 200),
        ]);

        $state = 'test-state-reject';
        session(['m365_oauth_state' => $state]);

        $response = $this->get(route('auth.m365.callback', [
            'code' => 'auth-code-reject',
            'state' => $state,
        ]));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(Auth::check());
        $this->assertDatabaseMissing('users', ['email' => $unauthorizedEmail]);
    }

    public function test_m365_callback_rejects_inactive_user(): void
    {
        Config::set('services.m365.client_id', 'test-client-id');
        Config::set('services.m365.client_secret', 'test-client-secret');
        Config::set('services.m365.tenant_id', 'common');

        $inactiveEmail = 'staf.dinyahaktif@akademisains.gov.my';
        User::create([
            'name' => 'Staf Tidak Aktif',
            'email' => $inactiveEmail,
            'role' => 'pemohon',
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        Http::fake([
            'https://login.microsoftonline.com/common/oauth2/v2.0/token' => Http::response([
                'access_token' => 'mock-access-token-inactive',
            ], 200),
            'https://graph.microsoft.com/v1.0/me*' => Http::response([
                'id' => 'm365-guid-inactive',
                'displayName' => 'Staf Tidak Aktif',
                'mail' => $inactiveEmail,
            ], 200),
        ]);

        $state = 'test-state-inactive';
        session(['m365_oauth_state' => $state]);

        $response = $this->get(route('auth.m365.callback', [
            'code' => 'auth-code-inactive',
            'state' => $state,
        ]));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(Auth::check());
    }

    public function test_m365_callback_rejects_invalid_state(): void
    {
        session(['m365_oauth_state' => 'valid-state-123']);

        $response = $this->get(route('auth.m365.callback', [
            'code' => 'any-code',
            'state' => 'tampered-state-999',
        ]));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(Auth::check());
    }
}
