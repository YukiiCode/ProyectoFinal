<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmación de Reserva - ReserTable</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .email-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 30px 20px;
        }
        .reservation-card {
            background-color: #f8fafc;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .reservation-details {
            display: grid;
            gap: 15px;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }
        .detail-value {
            font-weight: 500;
            color: #1f2937;
            font-size: 14px;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-confirmed {
            background-color: #d1fae5;
            color: #065f46;
        }
        .info-section {
            background-color: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .info-section h3 {
            margin: 0 0 10px;
            color: #1e40af;
            font-size: 16px;
        }
        .info-section p {
            margin: 0;
            color: #1e3a8a;
            font-size: 14px;
        }
        .footer {
            background-color: #f3f4f6;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        .footer a {
            color: #dc2626;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .header {
                padding: 20px 15px;
            }
            .content {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🍽️ ReserTable</h1>
            <p>¡Tu reserva ha sido confirmada!</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2 style="color: #dc2626; margin-top: 0;">Hola {{ $client->name }},</h2>
            
            <p>Nos complace confirmar tu reserva en <strong>La Trattoria</strong>. A continuación encontrarás todos los detalles:</p>

            <!-- Reservation Details Card -->
            <div class="reservation-card">
                <h3 style="margin-top: 0; color: #374151;">📋 Detalles de tu Reserva</h3>
                
                <div class="reservation-details">
                    <div class="detail-item">
                        <span class="detail-label">🗓️ Fecha</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">🕐 Hora</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('H:i') }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">🍽️ Mesa</span>
                        <span class="detail-value">Mesa {{ $table->table_number }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">👥 Comensales</span>
                        <span class="detail-value">{{ $reservation->party_size }} {{ $reservation->party_size == 1 ? 'persona' : 'personas' }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">⏱️ Duración</span>
                        <span class="detail-value">{{ $reservation->duration_hours ?? 1 }} {{ ($reservation->duration_hours ?? 1) == 1 ? 'hora' : 'horas' }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">📋 Estado</span>
                        <span class="detail-value">
                            <span class="status-badge {{ $reservation->status == 'confirmed' ? 'status-confirmed' : 'status-pending' }}">
                                {{ $reservation->status == 'confirmed' ? 'Confirmada' : 'Pendiente' }}
                            </span>
                        </span>
                    </div>

                    @if($reservation->special_requests)
                    <div class="detail-item">
                        <span class="detail-label">💬 Solicitudes especiales</span>
                        <span class="detail-value">{{ $reservation->special_requests }}</span>
                    </div>
                    @endif

                    @if($reservation->discount_coupon_id)
                    <div class="detail-item">
                        <span class="detail-label">🎟️ Cupón aplicado</span>
                        <span class="detail-value">{{ $reservation->discountCoupon->code ?? 'Descuento aplicado' }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Important Information -->
            <div class="info-section">
                <h3>ℹ️ Información Importante</h3>
                <p><strong>Dirección:</strong> Calle Principal 123, Centro, Ciudad</p>
                <p><strong>Teléfono:</strong> +34 123 456 789</p>
                <p><strong>Política de cancelación:</strong> Puedes cancelar tu reserva hasta 2 horas antes de la hora programada.</p>
            </div>

            <p>Si necesitas hacer algún cambio en tu reserva o tienes alguna pregunta, no dudes en contactarnos.</p>
            
            <p style="margin-bottom: 0;">¡Te esperamos para brindarte una experiencia gastronómica inolvidable!</p>
            
            <p style="margin-top: 20px;"><strong>El equipo de La Trattoria</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Este correo fue enviado automáticamente por ReserTable<br>
                <a href="mailto:{{ config('mail.from.address') }}">Contactar soporte</a> | 
                <a href="#">Política de privacidad</a>
            </p>
            <p style="margin-top: 10px; color: #9ca3af;">
                © {{ date('Y') }} ReserTable. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
