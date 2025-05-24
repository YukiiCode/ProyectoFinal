<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;

class TableController extends Controller
{
    // Devuelve el mapa de mesas como JSON
    public function map()
    {
        $tables = Table::all()->map(function ($table) {
            return [
                'id' => $table->id,
                'x' => $table->position_x,
                'y' => $table->position_y,
                'status' => $table->status,
                'capacity' => $table->capacity,
            ];
        });

        return response()->json($tables);
    }

    // Actualiza la posición de una mesa (solo empleados)
    public function update(Request $request, Table $table)
    {
        $request->validate([
            'position_x' => ['required', 'integer', 'between:0,100'],
            'position_y' => ['required', 'integer', 'between:0,100'],
        ]);

        $table->position_x = $request->position_x;
        $table->position_y = $request->position_y;
        $table->save();

        return response()->json($table);
    }
}