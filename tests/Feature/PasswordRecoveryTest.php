<?php

namespace Tests\Feature;

use App\Mail\PasswordRecoveryLink;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PasswordRecoveryTest extends TestCase
{
    private const GENERIC_STATUS = 'Se o e-mail estiver cadastrado, enviaremos um link';

    private const INVALID_TOKEN_MESSAGE = 'Este link de redefinição é inválido ou expirou. Solicite um novo link.';

    public static function accountTypes(): array
    {
        return [
            'student' => ['aluno', 'conta@example.test'],
            'professor' => ['professor', 'conta@example.test'],
        ];
    }

    public static function selectedAccountTypes(): array
    {
        return [
            'student' => ['aluno'],
            'professor' => ['professor'],
        ];
    }

    public static function invalidPasswords(): array
    {
        return [
            'short password' => [
                '12345',
                '12345',
                'A nova senha deve ter pelo menos 6 caracteres.',
            ],
            'mismatched confirmation' => [
                'senha-nova',
                'outra-senha',
                'A confirmação da senha não corresponde.',
            ],
        ];
    }

    #[DataProvider('selectedAccountTypes')]
    public function test_login_links_to_recovery_for_the_selected_account_type(string $tipo): void
    {
        $response = $this->get(route('login', ['tipo' => $tipo]));

        $response->assertOk()
            ->assertSee(route('password.request', ['tipo' => $tipo]), false);
    }

    public function test_recovery_form_displays_the_selected_account_type(): void
    {
        $response = $this->get(route('password.request', ['tipo' => 'professor']));

        $response->assertOk()
            ->assertViewIs('password-forgot')
            ->assertSee('Recuperação da senha de professor')
            ->assertSee('name="tipo" value="professor"', false)
            ->assertSee('name="email"', false);
    }

    #[DataProvider('accountTypes')]
    public function test_registered_account_receives_a_link_and_only_the_token_hash_is_stored(
        string $tipo,
        string $email,
    ): void {
        $this->createRecoveryTables();
        $this->createAccount('aluno', $email);
        $this->createAccount('professor', $email);
        Mail::fake();

        $response = $this->post(route('password.email'), [
            'email' => $email,
            'tipo' => $tipo,
        ]);

        $response->assertRedirect(route('password.request', ['tipo' => $tipo]))
            ->assertSessionHas('status', self::GENERIC_STATUS);

        $resetUrl = null;
        Mail::assertSent(PasswordRecoveryLink::class, function (PasswordRecoveryLink $mail) use ($email, &$resetUrl): bool {
            if (! $mail->hasTo($email)) {
                return false;
            }

            $resetUrl = $mail->resetUrl;

            return true;
        });

        $this->assertIsString($resetUrl);
        $urlParts = parse_url($resetUrl);
        parse_str($urlParts['query'] ?? '', $query);
        $token = basename($urlParts['path'] ?? '');
        $recoveryToken = DB::table('password_recovery_tokens')
            ->where('email', $email)
            ->where('tipo', $tipo)
            ->first();

        $this->assertSame($email, $query['email'] ?? null);
        $this->assertSame($tipo, $query['tipo'] ?? null);
        $this->assertSame('/redefinir-senha/'.$token, $urlParts['path'] ?? null);
        $this->assertNotSame('', $token);
        $this->assertNotNull($recoveryToken);
        $this->assertSame(hash('sha256', $token), $recoveryToken->token);
        $this->assertNotSame($token, $recoveryToken->token);
        $this->assertStringContainsString($token, (new PasswordRecoveryLink($resetUrl))->render());
    }

    #[DataProvider('selectedAccountTypes')]
    public function test_unknown_email_gets_the_same_confirmation_without_a_mail_or_token(string $tipo): void
    {
        $this->createRecoveryTables();
        Mail::fake();

        $response = $this->post(route('password.email'), [
            'email' => 'nao-cadastrado@example.test',
            'tipo' => $tipo,
        ]);

        $response->assertRedirect(route('password.request', ['tipo' => $tipo]))
            ->assertSessionHas('status', self::GENERIC_STATUS);

        Mail::assertNotSent(PasswordRecoveryLink::class);
        $this->assertDatabaseCount('password_recovery_tokens', 0);
    }

    public function test_valid_token_displays_the_password_reset_form(): void
    {
        $this->travelTo(Carbon::parse('2026-01-15 12:00:00'));
        $this->createRecoveryTables();
        $email = 'conta@example.test';
        $token = Str::random(64);
        $this->createAccount('aluno', $email);
        $this->createRecoveryToken($email, 'aluno', $token, now());

        $response = $this->get(route('password.reset', [
            'token' => $token,
            'email' => $email,
            'tipo' => 'aluno',
        ]));

        $response->assertOk()
            ->assertViewIs('password-reset')
            ->assertSee('name="password"', false)
            ->assertSee('name="password_confirmation"', false);
    }

    #[DataProvider('accountTypes')]
    public function test_password_reset_changes_only_the_selected_account_type(
        string $tipo,
        string $email,
    ): void {
        $this->travelTo(Carbon::parse('2026-01-15 12:00:00'));
        $this->createRecoveryTables();
        $this->createAccount('aluno', $email);
        $this->createAccount('professor', $email);
        $token = Str::random(64);
        $this->createRecoveryToken($email, $tipo, $token, now()->subMinutes(59));

        $response = $this->post(route('password.update', [
            'token' => $token,
            'email' => $email,
            'tipo' => $tipo,
        ]), [
            'email' => $email,
            'tipo' => $tipo,
            'password' => 'senha-nova',
            'password_confirmation' => 'senha-nova',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Senha redefinida com sucesso. Entre com sua nova senha.');

        $targetPassword = DB::table($this->accountTable($tipo))->where('email', $email)->value('password');
        $otherTipo = $tipo === 'aluno' ? 'professor' : 'aluno';
        $otherPassword = DB::table($this->accountTable($otherTipo))->where('email', $email)->value('password');

        $this->assertTrue(Hash::check('senha-nova', $targetPassword));
        $this->assertTrue(Hash::check('senha-antiga', $otherPassword));
        $this->assertDatabaseMissing('password_recovery_tokens', [
            'email' => $email,
            'tipo' => $tipo,
        ]);
    }

    public function test_login_displays_the_password_reset_success_message(): void
    {
        $response = $this->withSession([
            'status' => 'Senha redefinida com sucesso. Entre com sua nova senha.',
        ])->get(route('login'));

        $response->assertOk()
            ->assertSee('Senha redefinida com sucesso. Entre com sua nova senha.')
            ->assertSee('role="status"', false);
    }

    public function test_invalid_token_displays_a_friendly_error(): void
    {
        $this->createRecoveryTables();

        $response = $this->get(route('password.reset', [
            'token' => Str::random(64),
            'email' => 'conta@example.test',
            'tipo' => 'aluno',
        ]));

        $response->assertOk()
            ->assertSee(self::INVALID_TOKEN_MESSAGE)
            ->assertDontSee('name="password"', false);
    }

    public function test_expired_token_displays_a_friendly_error(): void
    {
        $this->travelTo(Carbon::parse('2026-01-15 12:00:00'));
        $this->createRecoveryTables();
        $email = 'conta@example.test';
        $token = Str::random(64);
        $this->createRecoveryToken($email, 'professor', $token, now()->subMinutes(61));

        $response = $this->get(route('password.reset', [
            'token' => $token,
            'email' => $email,
            'tipo' => 'professor',
        ]));

        $response->assertOk()
            ->assertSee(self::INVALID_TOKEN_MESSAGE)
            ->assertDontSee('name="password"', false);
    }

    public function test_expired_token_cannot_change_a_password(): void
    {
        $this->travelTo(Carbon::parse('2026-01-15 12:00:00'));
        $this->createRecoveryTables();
        $email = 'conta@example.test';
        $token = Str::random(64);
        $this->createAccount('aluno', $email);
        $this->createRecoveryToken($email, 'aluno', $token, now()->subMinutes(61));
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $email,
            'tipo' => 'aluno',
        ]);

        $response = $this->from($resetUrl)->post(route('password.update', [
            'token' => $token,
            'email' => $email,
            'tipo' => 'aluno',
        ]), [
            'email' => $email,
            'tipo' => 'aluno',
            'password' => 'senha-nova',
            'password_confirmation' => 'senha-nova',
        ]);

        $response->assertRedirect($resetUrl)
            ->assertSessionHasErrors(['token' => self::INVALID_TOKEN_MESSAGE]);

        $storedPassword = DB::table('alunos')->where('email', $email)->value('password');
        $this->assertTrue(Hash::check('senha-antiga', $storedPassword));
        $this->assertDatabaseHas('password_recovery_tokens', [
            'email' => $email,
            'tipo' => 'aluno',
        ]);
    }

    #[DataProvider('invalidPasswords')]
    public function test_password_reset_rejects_short_or_unconfirmed_passwords(
        string $password,
        string $confirmation,
        string $expectedError,
    ): void {
        $this->travelTo(Carbon::parse('2026-01-15 12:00:00'));
        $this->createRecoveryTables();
        $email = 'conta@example.test';
        $token = Str::random(64);
        $this->createAccount('aluno', $email);
        $this->createRecoveryToken($email, 'aluno', $token, now());

        $response = $this->post(route('password.update', [
            'token' => $token,
            'email' => $email,
            'tipo' => 'aluno',
        ]), [
            'email' => $email,
            'tipo' => 'aluno',
            'password' => $password,
            'password_confirmation' => $confirmation,
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors(['password' => $expectedError]);

        $storedPassword = DB::table('alunos')->where('email', $email)->value('password');
        $this->assertTrue(Hash::check('senha-antiga', $storedPassword));
        $this->assertDatabaseHas('password_recovery_tokens', [
            'email' => $email,
            'tipo' => 'aluno',
        ]);
    }

    public function test_recovery_request_rejects_an_unsupported_account_type(): void
    {
        $this->createRecoveryTables();
        Mail::fake();

        $response = $this->from(route('password.request'))->post(route('password.email'), [
            'email' => 'conta@example.test',
            'tipo' => 'admin',
        ]);

        $response->assertRedirect(route('password.request'))
            ->assertSessionHasErrors(['tipo' => 'Selecione aluno ou professor.']);

        Mail::assertNotSent(PasswordRecoveryLink::class);
        $this->assertDatabaseCount('password_recovery_tokens', 0);
    }

    private function createRecoveryTables(): void
    {
        Schema::dropIfExists('password_recovery_tokens');
        Schema::dropIfExists('alunos');
        Schema::dropIfExists('professores');

        Schema::create('password_recovery_tokens', function (Blueprint $table): void {
            $table->string('email', 254);
            $table->string('tipo', 20);
            $table->string('token', 64);
            $table->timestamp('created_at');
            $table->primary(['email', 'tipo']);
        });

        foreach (['alunos', 'professores'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table): void {
                $table->id();
                $table->string('email', 254);
                $table->string('password');
            });
        }
    }

    private function createAccount(string $tipo, string $email): void
    {
        DB::table($this->accountTable($tipo))->insert([
            'email' => $email,
            'password' => Hash::make('senha-antiga'),
        ]);
    }

    private function createRecoveryToken(string $email, string $tipo, string $token, Carbon $createdAt): void
    {
        DB::table('password_recovery_tokens')->insert([
            'email' => $email,
            'tipo' => $tipo,
            'token' => hash('sha256', $token),
            'created_at' => $createdAt,
        ]);
    }

    private function accountTable(string $tipo): string
    {
        return match ($tipo) {
            'aluno' => 'alunos',
            'professor' => 'professores',
            default => throw new \InvalidArgumentException('Unsupported account type.'),
        };
    }
}
