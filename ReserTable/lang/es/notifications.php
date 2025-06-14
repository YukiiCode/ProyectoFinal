<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Líneas de idioma para notificaciones
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas de idioma se usan para las notificaciones
    | que se muestran al usuario mediante el sistema de toast.
    |
    */

    // Tipos básicos
    'success' => 'Éxito',
    'error' => 'Error',
    'warning' => 'Advertencia',
    'info' => 'Información',

    // Autenticación
    'auth' => [
        'login_required' => 'Inicio de sesión requerido',
        'login_required_detail' => 'Para hacer una reserva necesitas iniciar sesión o registrarte.',
        'session_expired' => 'Sesión expirada',
        'session_expired_detail' => 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
        'unauthorized' => 'No autorizado',
        'unauthorized_detail' => 'No tienes permisos para realizar esta acción.',
    ],

    // Reservas
    'reservations' => [
        'confirmed' => 'Reserva confirmada',
        'created_success' => 'Tu reserva ha sido creada exitosamente.',
        'updated' => 'Reserva actualizada',
        'updated_success' => 'Los detalles de tu reserva han sido actualizados.',
        'cancelled' => 'Reserva cancelada',
        'cancelled_info' => 'Tu reserva ha sido cancelada.',
        'error' => 'Error en reserva',
        'processing_error' => 'Hubo un problema al procesar tu reserva. Por favor, inténtalo de nuevo.',
    ],

    // Mesas
    'tables' => [
        'position_updated' => 'Posición actualizada',
        'table_repositioned' => 'La mesa :tableNumber ha sido reposicionada correctamente.',
        'update_error' => 'Error de actualización',
        'position_update_failed' => 'No se pudo actualizar la posición de la mesa.',
        'table_created' => 'Mesa creada',
        'table_created_success' => 'La mesa :tableNumber ha sido creada correctamente.',
        'table_deleted' => 'Mesa eliminada',
        'table_deleted_success' => 'La mesa :tableNumber ha sido eliminada.',
        'delete_error' => 'Error al eliminar',
        'delete_failed' => 'No se pudo eliminar la mesa.',
    ],

    // Menú
    'menu' => [
        'item_saved' => 'Elemento guardado',
        'item_saved_success' => 'El elemento :itemName ha sido guardado correctamente.',
        'item_deleted' => 'Elemento eliminado',
        'item_deleted_success' => 'El elemento :itemName ha sido eliminado.',
    ],

    // Formularios
    'forms' => [
        'saved_success' => 'Guardado exitoso',
        'item_saved' => 'El :itemType ha sido guardado correctamente.',
        'form_error' => 'Error en formulario',
        'check_data' => 'Por favor, revisa los datos ingresados.',
        'incomplete_data' => 'Datos incompletos',
        'validation_errors' => 'Hay errores en el formulario que deben ser corregidos.',
    ],

    // Red y conexión
    'network' => [
        'connection_error' => 'Error de conexión',
        'connection_failed' => 'No se pudo conectar con el servidor. Verifica tu conexión a internet.',
        'unauthorized' => 'Sesión expirada',
        'session_expired' => 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
    ],
];
