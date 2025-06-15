<?php

use Illuminate\Support\Facades\Mail;

// Comando para probar el envío de correos
Mail::raw('Esto es una prueba de correo desde ReserTable

¡Hola! 

Este es un correo de prueba para verificar que la configuración de Ionos está funcionando correctamente.

✅ Configuración SMTP: smtp.ionos.es
✅ Puerto: 587 (TLS)
✅ Desde: help@yuki180.es

Si recibes este correo, significa que el sistema de notificaciones de ReserTable está funcionando perfectamente.

Ahora podrás recibir:
- Confirmaciones de reserva
- Recordatorios de citas
- Notificaciones de cambios
- Emails promocionales (si están activados)

¡Gracias por usar ReserTable!

---
ReserTable - Sistema de Reservas
https://reservtable.com', function ($message) {
    $message->to('fernandonieves180@gmail.com')
            ->subject('✅ Prueba de configuración de correo - ReserTable')
            ->from('help@yuki180.es', 'ReserTable - Sistema de Reservas');
});

echo "Correo enviado correctamente a fernandonieves180@gmail.com!\n";
