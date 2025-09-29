<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Alimento;
use Illuminate\Support\Facades\Http;

// ===========================
// Rotas de Alimentos CRUD
// ===========================

// Listar todos os alimentos
Route::get('/alimentos', function () {
    return response()->json(Alimento::all());
});

// Cadastrar alimento
Route::post('/alimentos', function (Request $request) {
    $request->validate([
        'nome' => 'required|string|max:255',
        'quantidade' => 'required|numeric|min:0.01',
        'unidade' => 'required|string|max:10',
    ]);

    $alimento = Alimento::create($request->only('nome', 'quantidade', 'unidade'));

    return response()->json($alimento, 201);
});

// Atualizar alimento
Route::put('/alimentos/{id}', function (Request $request, $id) {
    $alimento = Alimento::findOrFail($id);

    $request->validate([
        'nome' => 'sometimes|string|max:255',
        'quantidade' => 'sometimes|numeric|min:0.01',
        'unidade' => 'sometimes|string|max:10',
    ]);

    $alimento->update($request->only(['nome', 'quantidade', 'unidade']));

    return response()->json($alimento, 200);
});

// Deletar alimento
Route::delete('/alimentos/{id}', function ($id) {
    $alimento = Alimento::findOrFail($id);
    $alimento->delete();

    return response()->json(['message' => 'Alimento removido com sucesso'], 200);
});

// ===========================
// Gerar Receita com IA (Hugging Face)
// ===========================

Route::post('/receita', function () {

    $alimentos = Alimento::all()->map(function ($item) {
        return $item->quantidade . ' ' . $item->unidade . ' de ' . $item->nome;
    })->toArray();

    if (empty($alimentos)) {
        return response()->json(['error' => 'Não há alimentos cadastrados'], 400);
    }

    $ingredientes = implode(', ', $alimentos);

    $token = config('services.facehug.key');
    $url = config('services.facehug.url');
    
    if (!$token || !$url) {
        return response()->json(['error' => 'API KEY ou URL da Facehug não configuradas'], 500);
    }

    try {
        $response = Http::withToken($token)->post($url, [
            'inputs' => "Crie uma receita usando estes ingredientes: {$ingredientes}",
            'parameters' => [
                'max_new_tokens' => 300,
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $receita_texto = $data[0]['generated_text'] ?? ($data['generated_text'] ?? 'Receita não disponível');

            return response()->json([
                'ingredientes' => $alimentos,
                'receita' => $receita_texto,
            ]);
        }

        return response()->json([
            'error' => 'Não foi possível gerar a receita no momento.',
            'response_status' => $response->status(),
            'response_body' => $response->body(),
        ], 500);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erro ao conectar com a API: ' . $e->getMessage()
        ], 500);
    }
});
