<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use Illuminate\Http\JsonResponse;

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

    /**
     * Store a newly created table
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'table_number' => 'required|integer|unique:tables,table_number',
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'position_x' => 'nullable|integer|min:0',
            'position_y' => 'nullable|integer|min:0',
        ]);

        $table = Table::create([
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'status' => $request->status,
            'position_x' => $request->position_x ?? 0,
            'position_y' => $request->position_y ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mesa creada exitosamente',
            'table' => [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
                'status' => $table->status,
                'position_x' => $table->position_x,
                'position_y' => $table->position_y,
                'created_at' => $table->created_at,
                'updated_at' => $table->updated_at,
            ]
        ]);
    }

    // Actualiza la posición de una mesa (solo empleados)
    public function update(Request $request, Table $table)
    {
        // Si es solo actualización de posición
        if ($request->has('position_x') && $request->has('position_y') && !$request->has('table_number')) {
            $request->validate([
                'position_x' => ['required', 'integer', 'between:0,100'],
                'position_y' => ['required', 'integer', 'between:0,100'],
            ]);

            $table->position_x = $request->position_x;
            $table->position_y = $request->position_y;
            $table->save();

            return response()->json($table);
        }

        // Actualización completa de la mesa
        $request->validate([
            'table_number' => 'required|integer|unique:tables,table_number,' . $table->id,
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'position_x' => 'nullable|integer|min:0',
            'position_y' => 'nullable|integer|min:0',
        ]);

        $table->update([
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'status' => $request->status,
            'position_x' => $request->position_x ?? 0,
            'position_y' => $request->position_y ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mesa actualizada exitosamente',
            'table' => [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
                'status' => $table->status,
                'position_x' => $table->position_x,
                'position_y' => $table->position_y,
                'created_at' => $table->created_at,
                'updated_at' => $table->updated_at,
            ]
        ]);
    }

    /**
     * Remove the specified table
     */
    public function destroy(Table $table): JsonResponse
    {
        // Check if table has active reservations
        $activeReservations = $table->reservations()
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('reservation_date', '>=', now())
            ->count();

        if ($activeReservations > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la mesa porque tiene reservas activas'
            ], 400);
        }

        $table->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mesa eliminada exitosamente'
        ]);
    }

    /**
     * Update table status
     */
    public function updateStatus(Request $request, Table $table): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved,maintenance',
        ]);

        $table->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de la mesa actualizado exitosamente',
            'table' => [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
                'status' => $table->status,
                'position_x' => $table->position_x,
                'position_y' => $table->position_y,
                'created_at' => $table->created_at,
                'updated_at' => $table->updated_at,
            ]
        ]);
    }
}