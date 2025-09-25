<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Alimento;
use Illuminate\Support\Facades\Http;

// Listar alimentos
Route::get('/alimentos', function () {
    return response()->json(Alimento::all());
});

// Cadastrar alimento (nome, quantidade e unidade)
Route::post('/alimentos', function (Request $request) {
    $request->validate([
        'nome' => 'required|string|max:255',
        'quantidade' => 'required|numeric|min:0.01',
        'unidade' => 'required|string|max:10',
    ]);

    $alimento = Alimento::create([
        'nome' => $request->nome,
        'quantidade' => $request->quantidade,
        'unidade' => $request->unidade,
    ]);

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

// Gerar receita com IA (Facehug)
Route::post('/receita', function () {
    $alimentos = Alimento::all()->map(function($item){
        return $item->quantidade . ' ' . $item->unidade . ' de ' . $item->nome;
    })->toArray();

    $ingredientes = implode(', ', $alimentos);

    $response = Http::withToken(env('FACEHUG_API_KEY'))
        ->post(env('FACEHUG_API_URL'), [
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
});
