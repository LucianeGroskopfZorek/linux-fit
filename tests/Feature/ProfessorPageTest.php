<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProfessorPageTest extends TestCase
{
    public function test_professor_page_is_linked_from_the_homepage_navigation(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('professores.index'), false);
    }

    public function test_professor_page_displays_the_existing_professor_form(): void
    {
        $response = $this->get(route('professores.index'));

        $response->assertOk();
        $response->assertSee('Cadastro de');
        $response->assertSee('Professores');
    }
}
