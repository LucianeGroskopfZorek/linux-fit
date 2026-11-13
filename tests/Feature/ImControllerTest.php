<?php

namespace Tests\Feature;

use Tests\TestCase;

class ImControllerTest extends TestCase
{
    public function test_calculator_page_is_available(): void
    {
        $response = $this->get(route('imc.index'));

        $response->assertOk();
        $response->assertSee('Calculadora de');
    }

    public function test_calculator_returns_result_for_valid_measurements(): void
    {
        $response = $this->from(route('imc.index'))->post(route('imc.calculate'), [
            'weight' => 70,
            'height' => 1.75,
        ]);

        $response->assertRedirect(route('imc.index'));
        $response->assertSessionHas('bmi', 22.857142857142858);
        $response->assertSessionHas('category', 'Peso normal');

        $page = $this->followingRedirects()->post(route('imc.calculate'), [
            'weight' => 70,
            'height' => 1.75,
        ]);

        $page->assertOk();
        $page->assertSee('22,9');
        $page->assertSee('Peso normal');
        $page->assertDontSee('Acompanhe sua saúde');
        $page->assertDontSee('Abaixo de 18,5');
    }

    public function test_calculator_rejects_invalid_measurements(): void
    {
        $response = $this->from(route('imc.index'))->post(route('imc.calculate'), [
            'weight' => 0,
            'height' => 0,
        ]);

        $response->assertRedirect(route('imc.index'));
        $response->assertSessionHasErrors(['weight', 'height']);
    }
}
