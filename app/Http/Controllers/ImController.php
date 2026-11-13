<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImController extends Controller
{
    public function index(): View
    {
        return view('imc');
    }

    public function calculate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'weight' => ['required', 'numeric', 'min:1', 'max:500'],
            'height' => ['required', 'numeric', 'min:0.50', 'max:3'],
        ]);

        $weight = (float) $validated['weight'];
        $height = (float) $validated['height'];
        $bmi = $weight / ($height * $height);

        if ($bmi < 18.5) {
            $category = 'Abaixo do peso';
        } elseif ($bmi < 25) {
            $category = 'Peso normal';
        } elseif ($bmi < 30) {
            $category = 'Sobrepeso';
        } else {
            $category = 'Obesidade';
        }

        return back()->with([
            'bmi' => $bmi,
            'category' => $category,
        ]);
    }
}
