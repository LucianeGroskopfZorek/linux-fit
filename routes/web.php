<?php
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\ImController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendarController;
use App\Http\Controllers\AgendamentoAulasController;

Route::get('/', [\App\Http\Controllers\PlanoController::class, 'home']);

Route::get('/imc', [ImController::class, 'index'])->name('imc.index');
Route::post('/imc/calculate', [ImController::class, 'calculate'])->name('imc.calculate');

Route::get('/about', function () {
    return view('about');
}); 

Route::get('/contact', function () {
    return view('contact');
});

Route::get('/privacy', function () {
    return view('privacy');
});

Route::get('/terms', function () {
    return view('terms');
});

Route::get('/faq', function () {
    return view('faq');
});

Route::get('/agendamento', [AgendarController::class, 'index']);

Route::get('/alunos', [AlunoController::class, 'index'])->name('alunos.index');

Route::get('/aulas', [AgendamentoAulasController::class, 'index'])->name('aulas.index');
Route::post('/aulas/agendar', [AgendamentoAulasController::class, 'agendar'])->name('aulas.agendar');
Route::post('/aulas/cancelar', [AgendamentoAulasController::class, 'cancelar'])->name('aulas.cancelar');

Route::post('/contact', [ContatoController::class, 'enviar'])->name('contato.enviar');
Route::get('/planos', [\App\Http\Controllers\PlanoController::class, 'index']);
Route::get('/planos/{id}', [\App\Http\Controllers\PlanoController::class, 'show'])->name('planos.show');
Route::post('/alunos', [AlunoController::class, 'create'])->name('alunos.create');
Route::get('/alunos', [AlunoController::class, 'index'])->name('alunos.index');
Route::get('/professores', [ProfessorController::class, 'index'])
    ->name('professores.index');

Route::post('/professores', [ProfessorController::class, 'create'])
    ->name('professores.create');