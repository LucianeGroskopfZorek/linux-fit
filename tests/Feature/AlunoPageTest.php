<?php

namespace Tests\Feature;

use Tests\TestCase;

class AlunoPageTest extends TestCase
{
    public function test_student_page_is_linked_from_the_homepage_navigation(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('alunos.index'), false);
    }

    public function test_student_page_displays_the_existing_registration_form(): void
    {
        $response = $this->get(route('alunos.index'));

        $response->assertOk();
        $response->assertSee('Cadastro de');
        $response->assertSee('Alunos');
        $response->assertSee('name="email"', false);
        $response->assertSee('type="password"', false);
        $response->assertSee('name="password"', false);
    }
}
