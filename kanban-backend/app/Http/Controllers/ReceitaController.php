<?php

namespace App\Http\Controllers;

use App\Models\Alimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReceitaController extends Controller
{
    public function gerarReceita(Request $request)
    {
        // Pega todos os alimentos do banco
        $alimentos = Alimento::all()->pluck('nome')->toArray();
        $ingredientes = implode(', ', $alimentos);

        // Chama a API do Facehug (simulação, você precisa colocar a URL certa)
        $response = Http::post('https://api.facehug.ai/generate', [
            'prompt' => "Crie uma receita usando estes ingredientes: {$ingredientes}",
            'max_tokens' => 300,
        ]);

        if ($response->successful()) {
            return response()->json([
                'ingredientes' => $alimentos,
                'receita' => $response->json(),
            ]);
        }

        return response()->json([
            'error' => 'Não foi possível gerar a receita no momento.'
        ], 500);
    }
}
