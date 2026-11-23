<?php

namespace Tests\Feature;

use App\Models\Professor;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProfessorAuthenticationTest extends TestCase
{
    public function test_registration_page_offers_student_and_professor_options(): void
    {
        $response = $this->get(route('alunos.index'));

        $response->assertOk()
            ->assertSee('Sou aluno')
            ->assertSee('Sou professor')
            ->assertSee('action="/alunos"', false);
    }

    public function test_professor_registration_option_displays_professor_fields(): void
    {
        $response = $this->get(route('alunos.index', ['tipo' => 'professor']));

        $response->assertOk()
            ->assertSee(route('professores.cadastro'), false)
            ->assertSee('name="modalidades"', false)
            ->assertSee('name="especialidades"', false);
    }

    public function test_professor_login_route_redirects_to_the_unified_login(): void
    {
        $response = $this->get(route('professor.login'));

        $response->assertRedirect(route('login', ['tipo' => 'professor']));
    }

    public function test_unified_login_defaults_to_the_student_form(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertViewIs('login')
            ->assertSee('Sou aluno')
            ->assertSee('Sou professor')
            ->assertSee('Entrar')
            ->assertSee('class="cadastro-opcao ativo"', false)
            ->assertSee('Área do Aluno')
            ->assertSee(route('login.entrar'), false);
    }

    public function test_unified_login_selects_the_professor_form_from_its_query(): void
    {
        $response = $this->get(route('login', ['tipo' => 'professor']));

        $response->assertOk()
            ->assertSee('class="cadastro-opcao ativo"', false)
            ->assertSee('Área do Professor')
            ->assertSee(route('professor.entrar'), false);
    }

    public function test_login_redirects_a_logged_in_student_to_the_student_area(): void
    {
        $response = $this->withSession(['aluno_id' => 42])->get(route('login'));

        $response->assertRedirect(route('minha-area'));
    }

    public function test_login_redirects_a_logged_in_professor_to_the_professor_area(): void
    {
        $response = $this->withSession(['professor_id' => 18])->get(route('login'));

        $response->assertRedirect(route('professor.minha-area'));
    }

    public function test_student_login_failure_returns_to_the_student_form(): void
    {
        Schema::dropIfExists('alunos');
        Schema::create('alunos', function (Blueprint $table): void {
            $table->id();
            $table->string('email');
            $table->string('password');
        });

        $response = $this->from(route('login'))->post(route('login.entrar'), [
            'email' => 'aluno@example.test',
            'password' => 'senha-errada',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'E-mail ou senha incorretos.',
            ]);
    }

    public function test_valid_registration_stores_professor_with_a_hashed_password(): void
    {
        $this->createProfessorTable();

        $response = $this->from(route('alunos.index'))->post(
            route('professores.cadastro'),
            $this->professorPayload(),
        );

        $response->assertRedirect(route('professor.login'))
            ->assertSessionHas('sucesso', 'Professor cadastrado com sucesso. Faça login para continuar.');

        $this->assertDatabaseHas('professores', [
            'nome' => 'Professora Exemplo',
            'modalidades' => 'Pilates',
            'especialidades' => 'Reabilitação',
            'email' => 'professora@example.test',
        ]);

        $storedPassword = DB::table('professores')
            ->where('email', 'professora@example.test')
            ->value('password');

        $this->assertIsString($storedPassword);
        $this->assertTrue(Hash::check('senha-segura', $storedPassword));
    }

    public function test_registration_rejects_an_email_already_used_by_a_professor(): void
    {
        $this->createProfessorTable();
        $this->createProfessor();

        $response = $this->from(route('alunos.index'))->post(
            route('professores.cadastro'),
            $this->professorPayload(),
        );

        $response->assertRedirect(route('alunos.index'))
            ->assertSessionHasErrors([
                'email' => 'Este e-mail já está cadastrado para outro professor.',
            ]);

        $this->assertDatabaseCount('professores', 1);
    }

    public function test_registration_rejects_an_invalid_email(): void
    {
        $this->createProfessorTable();

        $response = $this->from(route('alunos.index'))->post(
            route('professores.cadastro'),
            $this->professorPayload(['email' => 'email-invalido']),
        );

        $response->assertRedirect(route('alunos.index'))
            ->assertSessionHasErrors([
                'email' => 'Informe um endereço de e-mail válido.',
            ]);

        $this->assertDatabaseCount('professores', 0);
    }

    public function test_registration_rejects_a_password_shorter_than_six_characters(): void
    {
        $this->createProfessorTable();

        $response = $this->from(route('alunos.index'))->post(
            route('professores.cadastro'),
            $this->professorPayload(['password' => '12345']),
        );

        $response->assertRedirect(route('alunos.index'))
            ->assertSessionHasErrors([
                'password' => 'A senha deve ter pelo menos 6 caracteres.',
            ]);

        $this->assertDatabaseCount('professores', 0);
    }

    public function test_registration_requires_all_professor_fields(): void
    {
        $this->createProfessorTable();

        $response = $this->from(route('alunos.index'))->post(
            route('professores.cadastro'),
            ['tipo_cadastro' => 'professor'],
        );

        $response->assertRedirect(route('alunos.index'))
            ->assertSessionHasErrors([
                'nome' => 'Informe o nome do professor.',
                'email' => 'Informe o e-mail do professor.',
                'modalidades' => 'Informe as modalidades do professor.',
                'especialidades' => 'Informe as especialidades do professor.',
                'password' => 'Digite a senha do professor.',
            ]);

        $this->assertDatabaseCount('professores', 0);
    }

    public function test_professor_login_sets_only_the_professor_session_key(): void
    {
        $this->createProfessorTable();
        $professor = $this->createProfessor();

        $response = $this->withSession(['aluno_id' => 42])->post(
            route('professor.entrar'),
            [
                'email' => 'professora@example.test',
                'password' => 'senha-segura',
            ],
        );

        $response->assertRedirect(route('professor.minha-area'))
            ->assertSessionHas('professor_id', $professor->id)
            ->assertSessionHas('aluno_id', 42);
    }

    public function test_professor_login_rejects_an_incorrect_password(): void
    {
        $this->createProfessorTable();
        $this->createProfessor();

        $response = $this->from(route('login', ['tipo' => 'professor']))->post(
            route('professor.entrar'),
            [
                'email' => 'professora@example.test',
                'password' => 'senha-errada',
            ],
        );

        $response->assertRedirect(route('login', ['tipo' => 'professor']))
            ->assertSessionHasErrors([
                'email' => 'E-mail ou senha incorretos.',
            ])
            ->assertSessionMissing('professor_id');
    }

    public function test_professor_area_displays_the_logged_in_professor_details(): void
    {
        $this->createProfessorTable();
        $professor = $this->createProfessor();

        $response = $this->withSession(['professor_id' => $professor->id])
            ->get(route('professor.minha-area'));

        $response->assertOk()
            ->assertViewIs('professor-minha-area')
            ->assertSee('Professora Exemplo')
            ->assertSee('professora@example.test')
            ->assertSee('Pilates')
            ->assertSee('Reabilitação')
            ->assertDontSee($professor->password);
    }

    public function test_professor_area_escapes_professor_supplied_text(): void
    {
        $this->createProfessorTable();
        $professor = Professor::create([
            'nome' => '<script>profile-name</script>',
            'modalidades' => '<img src=x onerror=profile>',
            'especialidades' => '<svg>profile-specialty</svg>',
            'email' => 'professora@example.test',
            'password' => Hash::make('senha-segura'),
        ]);

        $response = $this->withSession(['professor_id' => $professor->id])
            ->get(route('professor.minha-area'));

        $response->assertOk()
            ->assertSee('&lt;script&gt;profile-name&lt;/script&gt;', false)
            ->assertSee('&lt;img src=x onerror=profile&gt;', false)
            ->assertSee('&lt;svg&gt;profile-specialty&lt;/svg&gt;', false)
            ->assertDontSee('<script>profile-name</script>', false)
            ->assertDontSee('<img src=x onerror=profile>', false)
            ->assertDontSee('<svg>profile-specialty</svg>', false);
    }

    public function test_professor_area_redirects_when_the_professor_is_not_logged_in(): void
    {
        $this->createProfessorTable();

        $response = $this->get(route('professor.minha-area'));

        $response->assertRedirect(route('professor.login'));
    }

    public function test_professor_logout_preserves_the_student_session(): void
    {
        $response = $this->withSession([
            'professor_id' => 18,
            'aluno_id' => 42,
        ])->post(route('professor.sair'));

        $response->assertRedirect(route('professor.login'))
            ->assertSessionMissing('professor_id')
            ->assertSessionHas('aluno_id', 42);
    }

    /**
     * @param array<string, string> $overrides
     * @return array<string, string>
     */
    private function professorPayload(array $overrides = []): array
    {
        return array_merge([
            'tipo_cadastro' => 'professor',
            'nome' => 'Professora Exemplo',
            'email' => 'professora@example.test',
            'modalidades' => 'Pilates',
            'especialidades' => 'Reabilitação',
            'password' => 'senha-segura',
        ], $overrides);
    }

    private function createProfessorTable(): void
    {
        Schema::dropIfExists('professores');
        Schema::create('professores', function (Blueprint $table): void {
            $table->id();
            $table->string('nome', 100);
            $table->string('modalidades', 100);
            $table->string('especialidades', 100);
            $table->string('email', 254)->unique();
            $table->string('password');
        });
    }

    private function createProfessor(): Professor
    {
        return Professor::create([
            'nome' => 'Professora Exemplo',
            'modalidades' => 'Pilates',
            'especialidades' => 'Reabilitação',
            'email' => 'professora@example.test',
            'password' => Hash::make('senha-segura'),
        ]);
    }
}
