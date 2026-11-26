<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->nullable();
            $table->string('account_type')->nullable();
            $table->string('account_status')->nullable();
            $table->string('cloak_alias')->nullable();
            $table->timestamps();
        });
    }

    public function test_google_signup_redirect_preserves_its_intent(): void
    {
        config([
            'services.google.client_id' => 'client-id',
            'services.google.client_secret' => 'client-secret',
        ]);
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(new RedirectResponse('https://accounts.google.com/oauth'));
        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $this->get(route('auth.google.redirect', ['intent' => 'signup']))
            ->assertRedirect('https://accounts.google.com/oauth')
            ->assertSessionHas('google_auth_intent', 'signup');
    }

    public function test_login_and_signup_forms_render_google_actions_with_their_own_intents(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('auth.google.redirect', ['intent' => 'login']), false)
            ->assertSee(route('auth.google.redirect', ['intent' => 'signup']), false);
    }

    public function test_google_redirect_reports_missing_client_configuration(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $this->get(route('auth.google.redirect', ['intent' => 'signup']))
            ->assertRedirect(route('register'))
            ->assertSessionHas('error', 'Google sign-in is not configured. Please use your email and password or contact the administrator.');
    }

    public function test_google_login_authenticates_only_an_existing_user_with_a_verified_email(): void
    {
        $user = User::query()->create([
            'name' => 'Ava Santos',
            'email' => 'ava@psu.edu.ph',
            'password' => 'hashed-password',
            'role' => 'student',
            'account_type' => 'student',
            'account_status' => 'active',
        ]);
        $this->mockGoogleUser($this->verifiedGoogleUser('AVA@PSU.EDU.PH'));

        $this->withSession(['google_auth_intent' => 'login'])
            ->get(route('auth.google.callback'))
            ->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_google_login_does_not_create_an_unregistered_user(): void
    {
        $this->mockGoogleUser($this->verifiedGoogleUser('new@psu.edu.ph'));

        $this->withSession(['google_auth_intent' => 'login'])
            ->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'This Google email is not registered yet. Please choose Sign up to complete your account details.');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'new@psu.edu.ph']);
    }

    public function test_google_signup_only_prefills_the_registration_form(): void
    {
        $this->mockGoogleUser($this->verifiedGoogleUser('21ava@psu.edu.ph'));

        $this->withSession(['google_auth_intent' => 'signup'])
            ->get(route('auth.google.callback'))
            ->assertRedirect(route('register'));

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('value="Ava"', false)
            ->assertSee('value="Marie"', false)
            ->assertSee('value="Santos"', false)
            ->assertSee('value="21ava@psu.edu.ph"', false);

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => '21ava@psu.edu.ph']);
    }

    public function test_google_login_rejects_an_unverified_email(): void
    {
        $this->mockGoogleUser($this->verifiedGoogleUser('user@psu.edu.ph', false));

        $this->withSession(['google_auth_intent' => 'login'])
            ->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Google did not provide a verified email address. Please try again with a verified account.');

        $this->assertGuest();
    }

    private function verifiedGoogleUser(string $email, bool $verified = true): GoogleUser
    {
        return GoogleUser::fake([
            'email' => $email,
            'email_verified' => $verified,
            'name' => 'Ava Marie Santos',
            'given_name' => 'Ava',
            'middle_name' => 'Marie',
            'family_name' => 'Santos',
        ]);
    }

    private function mockGoogleUser(GoogleUser $user): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($user);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);
    }
}
