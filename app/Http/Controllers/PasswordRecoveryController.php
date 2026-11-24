<?php

namespace App\Http\Controllers;

use App\Mail\PasswordRecoveryLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;

class PasswordRecoveryController extends Controller
{
    private const ACCOUNT_TYPES = ['aluno', 'professor'];

    private const TOKEN_LIFETIME_MINUTES = 60;

    private const GENERIC_STATUS = 'Se o e-mail estiver cadastrado, enviaremos um link';

    private const INVALID_TOKEN_MESSAGE = 'Este link de redefinição é inválido ou expirou. Solicite um novo link.';

    public function requestForm(Request $request): View
    {
        $tipo = $request->query('tipo', 'aluno');

        if (! is_string($tipo) || ! in_array($tipo, self::ACCOUNT_TYPES, true)) {
            $tipo = 'aluno';
        }

        return view('password-forgot', ['tipo' => $tipo]);
    }

    public function sendLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:254'],
            'tipo' => ['required', Rule::in(self::ACCOUNT_TYPES)],
        ], [
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'tipo.required' => 'Selecione aluno ou professor.',
            'tipo.in' => 'Selecione aluno ou professor.',
        ]);

        $email = $validated['email'];
        $tipo = $validated['tipo'];

        if ($this->findAccount($tipo, $email) !== null) {
            $token = Str::random(64);

            DB::table('password_recovery_tokens')->upsert([
                [
                    'email' => $email,
                    'tipo' => $tipo,
                    'token' => hash('sha256', $token),
                    'created_at' => now(),
                ],
            ], ['email', 'tipo'], ['token', 'created_at']);

            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $email,
                'tipo' => $tipo,
            ]);

            Mail::to($email)->send(new PasswordRecoveryLink($resetUrl));
        }

        return redirect()
            ->route('password.request', ['tipo' => $tipo])
            ->with('status', self::GENERIC_STATUS);
    }

    public function resetForm(Request $request, string $token): View
    {
        $validator = Validator::make($request->query(), [
            'email' => ['required', 'email', 'max:254'],
            'tipo' => ['required', Rule::in(self::ACCOUNT_TYPES)],
        ]);

        $contextIsValid = $validator->passes();
        $context = $contextIsValid ? $validator->validated() : [];
        $email = $context['email'] ?? '';
        $tipo = $context['tipo'] ?? 'aluno';
        $tokenIsValid = $contextIsValid
            && $this->isValidToken($this->findRecoveryToken($email, $tipo), $token);

        return view('password-reset', [
            'email' => $email,
            'tipo' => $tipo,
            'token' => $token,
            'tokenIsValid' => $tokenIsValid,
        ]);
    }

    public function updatePassword(Request $request, string $token): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:254'],
            'tipo' => ['required', Rule::in(self::ACCOUNT_TYPES)],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'tipo.required' => 'Selecione aluno ou professor.',
            'tipo.in' => 'Selecione aluno ou professor.',
            'password.required' => 'Informe a nova senha.',
            'password.min' => 'A nova senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
        ]);

        $email = $validated['email'];
        $tipo = $validated['tipo'];
        $password = $validated['password'];
        $passwordWasUpdated = DB::transaction(function () use ($email, $tipo, $password, $token): bool {
            $recoveryToken = DB::table('password_recovery_tokens')
                ->where('email', $email)
                ->where('tipo', $tipo)
                ->lockForUpdate()
                ->first();

            if (! $this->isValidToken($recoveryToken, $token)) {
                return false;
            }

            $account = $this->findAccount($tipo, $email);

            if ($account === null) {
                return false;
            }

            $updatedRows = DB::table($this->accountTable($tipo))
                ->where('id', $account->id)
                ->update(['password' => Hash::make($password)]);

            if ($updatedRows !== 1) {
                return false;
            }

            DB::table('password_recovery_tokens')
                ->where('email', $email)
                ->where('tipo', $tipo)
                ->delete();

            return true;
        });

        if (! $passwordWasUpdated) {
            return redirect()
                ->route('password.reset', [
                    'token' => $token,
                    'email' => $email,
                    'tipo' => $tipo,
                ])
                ->withErrors(['token' => self::INVALID_TOKEN_MESSAGE]);
        }

        return redirect()
            ->route('login')
            ->with('status', 'Senha redefinida com sucesso. Entre com sua nova senha.');
    }

    private function findAccount(string $tipo, string $email): ?object
    {
        return DB::table($this->accountTable($tipo))
            ->where('email', $email)
            ->orderBy('id')
            ->first(['id']);
    }

    private function accountTable(string $tipo): string
    {
        return match ($tipo) {
            'aluno' => 'alunos',
            'professor' => 'professores',
            default => throw new InvalidArgumentException('Unsupported password recovery account type.'),
        };
    }

    private function findRecoveryToken(string $email, string $tipo): ?object
    {
        return DB::table('password_recovery_tokens')
            ->where('email', $email)
            ->where('tipo', $tipo)
            ->first();
    }

    private function isValidToken(?object $recoveryToken, string $token): bool
    {
        if (
            $recoveryToken === null
            || ! isset($recoveryToken->token, $recoveryToken->created_at)
            || ! is_string($recoveryToken->token)
            || ! hash_equals($recoveryToken->token, hash('sha256', $token))
        ) {
            return false;
        }

        $createdAt = Carbon::parse($recoveryToken->created_at);
        $now = now();

        return $createdAt->greaterThanOrEqualTo($now->copy()->subMinutes(self::TOKEN_LIFETIME_MINUTES))
            && $createdAt->lessThanOrEqualTo($now);
    }
}
