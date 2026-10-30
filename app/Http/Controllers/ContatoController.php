<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContatoController extends Controller
{
    public function enviar(Request $request)
    {
        $dados = $request->validate([
            'nome'     => 'required|string|max:100',
            'email'    => 'required|email',
            'assunto'  => 'required|string|max:150',
            'mensagem' => 'required|string|max:2000',
        ]);

        $texto = "Nome: {$dados['nome']}\nE-mail: {$dados['email']}\nAssunto: {$dados['assunto']}\n\nMensagem:\n{$dados['mensagem']}";

        Mail::raw($texto, function ($mail) use ($dados) {
            $mail->to('lucianeg02@gmail.com')
                 ->replyTo($dados['email'], $dados['nome'])
                 ->subject('Contato LinuxFit: ' . $dados['assunto']);
        });

        return redirect('/#contato')->with('sucesso', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }
}