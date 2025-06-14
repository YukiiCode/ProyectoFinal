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
    }    /**
     * Store a newly created table
     */
    public function store(Request $request)
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

        return back()->with('success', __('tables.created_successfully'));
    }// Actualiza la posición de una mesa (solo empleados)
    public function update(Request $request, Table $table)
    {
        // Si es solo actualización de posición (drag and drop)
        if ($request->has('position_x') && $request->has('position_y') && !$request->has('table_number')) {
            $request->validate([
                'position_x' => ['required', 'integer', 'between:0,100'],
                'position_y' => ['required', 'integer', 'between:0,100'],
            ]);

            $table->position_x = $request->position_x;
            $table->position_y = $request->position_y;
            $table->save();

            // Para drag and drop, devolver una respuesta simple que Inertia pueda manejar
            return back()->with('success', __('tables.position_updated_successfully'));
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

        return back()->with('success', __('tables.updated_successfully'));
    }    /**
     * Remove the specified table
     */
    public function destroy(Table $table)
    {
        // Check if table has active reservations
        $activeReservations = $table->reservations()
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('reservation_date', '>=', now())
            ->count();

        if ($activeReservations > 0) {
            return back()->withErrors(['message' => 'No se puede eliminar la mesa porque tiene reservas activas']);
        }

        $table->delete();

        return back()->with('success', __('tables.deleted_successfully'));
    }

    /**
     * Update table status
     */
    public function updateStatus(Request $request, Table $table)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved,maintenance',
        ]);

        $table->update(['status' => $request->status]);

        return back()->with('success', __('tables.status_updated_successfully'));
    }

    /**
     * Get tables for public access (for reservations map)
     */
    public function publicIndex()
    {
        $tables = Table::where('status', '!=', 'maintenance')
            ->orderBy('table_number')
            ->get()
            ->map(function ($table) {
                return [
                    'id' => $table->id,
                    'table_number' => $table->table_number,
                    'capacity' => $table->capacity,
                    'status' => $table->status,
                    'position_x' => $table->position_x,
                    'position_y' => $table->position_y,
                ];
            });

        return response()->json([
            'success' => true,
            'tables' => $tables
        ]);
    }
}