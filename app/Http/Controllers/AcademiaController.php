<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcademiaController extends Controller
{
  private function listModalidades(): array
  {
    return [
      [
        'id' => 1,
        'nome' => 'Musculação',
        'descricao' => 'Treinamento de força e hipertrofia muscular'
      ],
      [
        'id' => 2,
        'nome' => 'Crossfit',
        'descricao' => 'Treinamento funcional de alta intensidade'
      ],
      [
        'id' => 3,
        'nome' => 'Pilates',
        'descricao' => 'Treinamento de fortalecimento e alongamento muscular'
      ]
    ];
  }

  public function modalidades()
  {
    $modalidades = $this->listModalidades();
    return response()->json($modalidades);
  }
}
