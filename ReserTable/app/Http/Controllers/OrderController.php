<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        // Aquí deberías guardar el pedido en la base de datos.
        // Por ahora solo simula éxito y loguea el pedido.
        Log::info('Pedido recibido', $data);

        return response()->json(['message' => 'Pedido recibido correctamente.'], 201);
    }
}
