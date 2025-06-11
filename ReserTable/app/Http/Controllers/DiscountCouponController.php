<?php

namespace App\Http\Controllers;

use App\Models\DiscountCoupon;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Carbon\Carbon;

class DiscountCouponController extends Controller
{
    /**
     * Mostrar lista de cupones de descuento (Admin)
     */
    public function index()
    {
        $coupons = DiscountCoupon::with('client')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/DiscountCoupons/Index', [
            'coupons' => $coupons
        ]);
    }

    /**
     * Mostrar formulario para crear nuevo cupón
     */
    public function create()
    {
        $clients = Client::select('id', 'name', 'email')->get();
        
        return Inertia::render('Admin/DiscountCoupons/Create', [
            'clients' => $clients
        ]);
    }

    /**
     * Almacenar nuevo cupón de descuento
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:discount_coupons,code',
            'type' => 'required|in:global,personalized',
            'discount_type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'valid_from' => 'required|date|after_or_equal:today',
            'valid_to' => 'required|date|after:valid_from',
            'max_uses' => 'nullable|integer|min:1',
            'client_id' => [
                'nullable',
                'integer',
                'exists:clients,id',
                Rule::requiredIf($request->type === 'personalized')
            ]
        ]);        // Validaciones adicionales
        if ($request->discount_type === 'percentage' && $request->value > 100) {
            return back()->withErrors([
                'value' => __('validation.percentage_max_100')
            ]);
        }

        $coupon = DiscountCoupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'discount_type' => $request->discount_type,
            'value' => $request->value,
            'valid_from' => $request->valid_from,
            'valid_to' => $request->valid_to,
            'max_uses' => $request->max_uses,
            'used_count' => 0,
            'client_id' => $request->type === 'personalized' ? $request->client_id : null,
        ]);        return redirect()->route('admin.discount-coupons.index')
            ->with('success', __('coupons.created_successfully'));
    }

    /**
     * Mostrar detalles de un cupón específico
     */
    public function show(DiscountCoupon $discountCoupon)
    {
        $discountCoupon->load('client');
        
        return Inertia::render('Admin/DiscountCoupons/Show', [
            'coupon' => $discountCoupon
        ]);
    }

    /**
     * Mostrar formulario para editar cupón
     */
    public function edit(DiscountCoupon $discountCoupon)
    {
        $clients = Client::select('id', 'name', 'email')->get();
        
        return Inertia::render('Admin/DiscountCoupons/Edit', [
            'coupon' => $discountCoupon,
            'clients' => $clients
        ]);
    }

    /**
     * Actualizar cupón de descuento
     */
    public function update(Request $request, DiscountCoupon $discountCoupon)
    {
        $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('discount_coupons')->ignore($discountCoupon->id)
            ],
            'type' => 'required|in:global,personalized',
            'discount_type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'max_uses' => 'nullable|integer|min:1',
            'client_id' => [
                'nullable',
                'integer',
                'exists:clients,id',
                Rule::requiredIf($request->type === 'personalized')
            ]
        ]);        // Validaciones adicionales
        if ($request->discount_type === 'percentage' && $request->value > 100) {
            return back()->withErrors([
                'value' => __('validation.percentage_max_100')
            ]);
        }

        $discountCoupon->update([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'discount_type' => $request->discount_type,
            'value' => $request->value,
            'valid_from' => $request->valid_from,
            'valid_to' => $request->valid_to,
            'max_uses' => $request->max_uses,
            'client_id' => $request->type === 'personalized' ? $request->client_id : null,
        ]);        return redirect()->route('admin.discount-coupons.index')
            ->with('success', __('coupons.updated_successfully'));
    }

    /**
     * Eliminar cupón de descuento
     */
    public function destroy(DiscountCoupon $discountCoupon)
    {
        $discountCoupon->delete();
          return redirect()->route('admin.discount-coupons.index')
            ->with('success', __('coupons.deleted_successfully'));
    }

    /**
     * Generar código aleatorio para cupón
     */
    public function generateCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (DiscountCoupon::where('code', $code)->exists());

        return response()->json(['code' => $code]);
    }

    /**
     * Validar código de descuento (API para clientes)
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'client_id' => 'nullable|integer|exists:clients,id'
        ]);

        $validation = DiscountCoupon::validateForClient(
            $request->code, 
            $request->client_id
        );

        return response()->json($validation);
    }

    /**
     * Aplicar código de descuento a una reserva o pedido
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'client_id' => 'nullable|integer|exists:clients,id'
        ]);

        $validation = DiscountCoupon::validateForClient(
            $request->code, 
            $request->client_id
        );

        if (!$validation['valid']) {
            return response()->json($validation, 400);
        }

        $coupon = $validation['coupon'];
        $discountAmount = $coupon->calculateDiscount($request->amount);

        return response()->json([
            'valid' => true,
            'coupon' => $coupon,
            'discount_amount' => $discountAmount,
            'final_amount' => max(0, $request->amount - $discountAmount),
            'formatted_discount' => $coupon->formatted_discount
        ]);
    }

    /**
     * Generar estadísticas de cupones
     */
    public function getStatistics()
    {
        $stats = [
            'total_coupons' => DiscountCoupon::count(),
            'active_coupons' => DiscountCoupon::active()->count(),
            'expired_coupons' => DiscountCoupon::where('valid_to', '<', Carbon::today())->count(),
            'used_coupons' => DiscountCoupon::used()->count(),
            'global_coupons' => DiscountCoupon::global()->count(),
            'personalized_coupons' => DiscountCoupon::personalized()->count(),
            'total_savings' => DiscountCoupon::used()
                ->get()
                ->sum(function ($coupon) {
                    return $coupon->value * $coupon->used_count;
                }),
            'average_discount' => DiscountCoupon::avg('value'),
            'most_used_coupon' => DiscountCoupon::orderBy('used_count', 'desc')->first(),
        ];

        // Estadísticas por mes
        $monthlyStats = DiscountCoupon::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as total,
                SUM(used_count) as total_uses
            ')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'general' => $stats,
            'monthly' => $monthlyStats
        ]);
    }

    /**
     * Mostrar cupones disponibles para un cliente
     */
    public function getClientCoupons(Request $request)
    {
        $clientId = $request->query('client_id');
        
        if (!$clientId) {
            return response()->json([
                'coupons' => []
            ]);
        }

        $today = Carbon::today();
        
        $coupons = DiscountCoupon::where(function ($query) use ($clientId) {
                $query->where('type', 'global')
                      ->orWhere(function ($subQuery) use ($clientId) {
                          $subQuery->where('type', 'personalized')
                                   ->where('client_id', $clientId);
                      });
            })
            ->where('valid_from', '<=', $today)
            ->where('valid_to', '>=', $today)
            ->where(function ($query) {
                $query->whereNull('max_uses')
                      ->orWhereColumn('used_count', '<', 'max_uses');
            })
            ->select('id', 'code', 'discount_type', 'value', 'type', 'valid_to')
            ->get();

        return response()->json([
            'coupons' => $coupons
        ]);
    }

    /**
     * Crear cupón automático para cliente (rewards)
     */
    public function createRewardCoupon(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'discount_type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'days_valid' => 'integer|min:1|max:365'
        ]);

        $daysValid = $request->days_valid ?? 30;
        
        // Generar código único
        do {
            $code = 'REWARD' . strtoupper(Str::random(6));
        } while (DiscountCoupon::where('code', $code)->exists());        $coupon = DiscountCoupon::create([
            'code' => $code,
            'type' => 'personalized',
            'discount_type' => $request->discount_type,
            'value' => $request->value,
            'valid_from' => Carbon::today(),
            'valid_to' => Carbon::today()->addDays($daysValid),
            'max_uses' => 1,
            'used_count' => 0,
            'client_id' => $request->client_id,
        ]);        return response()->json([
            'success' => true,
            'coupon' => $coupon,
            'message' => __('coupons.reward_created_successfully')
        ]);
    }

    /**
     * Generar códigos de descuento masivos
     */
    public function generateBulkCoupons(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:100',
            'prefix' => 'nullable|string|max:10',
            'type' => 'required|in:global,personalized',
            'discount_type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'valid_from' => 'required|date|after_or_equal:today',
            'valid_to' => 'required|date|after:valid_from',
            'max_uses' => 'nullable|integer|min:1',
        ]);

        $coupons = [];
        $prefix = $request->prefix ?? 'BULK';        for ($i = 0; $i < $request->count; $i++) {
            $code = DiscountCoupon::generateUniqueCode($prefix);
            
            $coupon = DiscountCoupon::create([
                'code' => $code,
                'type' => $request->type,
                'discount_type' => $request->discount_type,
                'value' => $request->value,
                'valid_from' => $request->valid_from,
                'valid_to' => $request->valid_to,
                'max_uses' => $request->max_uses,
                'used_count' => 0,
                'client_id' => null,
            ]);

            $coupons[] = $coupon;
        }        return response()->json([
            'success' => true,
            'coupons' => $coupons,
            'message' => __('coupons.bulk_generated_successfully', ['count' => $request->count])
        ]);
    }

    /**
     * Marcar cupón como usado
     */
    public function markAsUsed(DiscountCoupon $discountCoupon)
    {
        $discountCoupon->markAsUsed();        return response()->json([
            'success' => true,
            'message' => __('coupons.marked_as_used'),
            'coupon' => $discountCoupon->fresh()
        ]);
    }

    /**
     * Obtener estadísticas para el dashboard de cupones
     */
    public function getStats()
    {
        $stats = [
            'total_coupons' => DiscountCoupon::count(),
            'active_coupons' => DiscountCoupon::active()->count(),
            'expired_coupons' => DiscountCoupon::where('valid_to', '<', Carbon::today())->count(),
            'used_coupons' => DiscountCoupon::used()->count(),
            'global_coupons' => DiscountCoupon::global()->count(),
            'personalized_coupons' => DiscountCoupon::personalized()->count(),
            'total_savings' => DiscountCoupon::used()
                ->get()
                ->sum(function ($coupon) {
                    return $coupon->value * $coupon->used_count;
                }),
            'average_discount' => round(DiscountCoupon::avg('value'), 2),
            'usage_rate' => DiscountCoupon::count() > 0 ? 
                round((DiscountCoupon::used()->count() / DiscountCoupon::count()) * 100, 2) : 0,
        ];

        // Estadísticas por mes (últimos 6 meses)
        $monthlyStats = DiscountCoupon::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as created,
                SUM(used_count) as total_uses,
                MONTHNAME(created_at) as month_name
            ')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('year', 'month', 'month_name')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Estadísticas por tipo de descuento
        $discountTypeStats = DiscountCoupon::selectRaw('
                discount_type,
                COUNT(*) as count,
                AVG(value) as avg_value,
                SUM(used_count) as total_uses
            ')
            ->groupBy('discount_type')
            ->get();

        return response()->json([
            'general' => $stats,
            'monthly' => $monthlyStats,
            'by_discount_type' => $discountTypeStats
        ]);
    }

    /**
     * Exportar cupones a CSV
     */
    public function export(Request $request)
    {
        $query = DiscountCoupon::with('client');

        // Filtros opcionales
        if ($request->has('status') && $request->status !== 'all') {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'expired':
                    $query->where('valid_to', '<', Carbon::today());
                    break;
                case 'used':
                    $query->used();
                    break;
            }
        }

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $coupons = $query->get();

        $filename = 'cupones_descuento_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($coupons) {
            $file = fopen('php://output', 'w');
            
            // Encabezados CSV
            fputcsv($file, [
                'ID',
                'Código',
                'Tipo',
                'Tipo de Descuento',
                'Valor',
                'Válido Desde',
                'Válido Hasta',
                'Usos Máximos',
                'Usos Actuales',
                'Estado',
                'Cliente',
                'Email Cliente',
                'Fecha Creación'
            ]);

            // Datos
            foreach ($coupons as $coupon) {
                fputcsv($file, [
                    $coupon->id,
                    $coupon->code,
                    $coupon->type === 'global' ? 'Global' : 'Personalizado',
                    $coupon->discount_type === 'percentage' ? 'Porcentaje' : 'Monto Fijo',
                    $coupon->value,
                    $coupon->valid_from->format('Y-m-d'),
                    $coupon->valid_to->format('Y-m-d'),
                    $coupon->max_uses ?? 'Sin límite',
                    $coupon->used_count,
                    $coupon->status_label,
                    $coupon->client ? $coupon->client->name : 'N/A',
                    $coupon->client ? $coupon->client->email : 'N/A',
                    $coupon->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generar reporte de cupones
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'in:json,pdf'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Datos del reporte
        $totalCoupons = DiscountCoupon::whereBetween('created_at', [$startDate, $endDate])->count();
        $usedCoupons = DiscountCoupon::whereBetween('created_at', [$startDate, $endDate])
            ->where('used_count', '>', 0)->count();
        
        $totalSavings = DiscountCoupon::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->sum(function ($coupon) {
                return $coupon->value * $coupon->used_count;
            });

        // Cupones más usados
        $topCoupons = DiscountCoupon::whereBetween('created_at', [$startDate, $endDate])
            ->where('used_count', '>', 0)
            ->with('client')
            ->orderBy('used_count', 'desc')
            ->limit(10)
            ->get();

        // Estadísticas por tipo
        $statsByType = DiscountCoupon::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                type,
                discount_type,
                COUNT(*) as count,
                SUM(used_count) as total_uses,
                AVG(value) as avg_value
            ')
            ->groupBy('type', 'discount_type')
            ->get();

        $report = [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'days' => $startDate->diffInDays($endDate) + 1
            ],
            'summary' => [
                'total_coupons' => $totalCoupons,
                'used_coupons' => $usedCoupons,
                'unused_coupons' => $totalCoupons - $usedCoupons,
                'usage_rate' => $totalCoupons > 0 ? round(($usedCoupons / $totalCoupons) * 100, 2) : 0,
                'total_savings' => $totalSavings,
                'avg_savings_per_use' => $usedCoupons > 0 ? round($totalSavings / $usedCoupons, 2) : 0
            ],
            'top_coupons' => $topCoupons,
            'stats_by_type' => $statsByType,
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }

    /**
     * Limpiar cupones expirados
     */
    public function cleanupExpired(Request $request)
    {
        $request->validate([
            'days_expired' => 'integer|min:0|max:365',
            'confirm' => 'required|boolean'
        ]);        if (!$request->confirm) {
            return response()->json([
                'success' => false,
                'message' => __('coupons.must_confirm_cleanup')
            ], 400);
        }

        $daysExpired = $request->days_expired ?? 30;
        $cutoffDate = Carbon::now()->subDays($daysExpired);

        // Encontrar cupones expirados
        $expiredCoupons = DiscountCoupon::where('valid_to', '<', $cutoffDate)
            ->where('used_count', 0); // Solo cupones no utilizados

        $count = $expiredCoupons->count();
          if ($count === 0) {
            return response()->json([
                'success' => true,
                'message' => __('coupons.no_expired_found'),
                'deleted_count' => 0
            ]);
        }

        // Obtener información antes de eliminar
        $deletedCoupons = $expiredCoupons->get(['id', 'code', 'valid_to', 'created_at']);
        
        // Eliminar cupones expirados
        $expiredCoupons->delete();        return response()->json([
            'success' => true,
            'message' => __('coupons.expired_deleted_successfully', ['count' => $count]),
            'deleted_count' => $count,
            'deleted_coupons' => $deletedCoupons,
            'cutoff_date' => $cutoffDate->format('Y-m-d')
        ]);
    }
}
