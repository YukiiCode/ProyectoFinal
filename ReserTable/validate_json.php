<?php
$json = file_get_contents('resources/js/i18n/locales/es.json');
$data = json_decode($json);

if (json_last_error() === JSON_ERROR_NONE) {
    echo "JSON es válido\n";
    
    // Verificar si existe la clave reservations.pending
    if (isset($data->reservations->pending)) {
        echo "reservations.pending existe: " . $data->reservations->pending . "\n";
    } else {
        echo "reservations.pending NO existe\n";
    }
} else {
    echo "JSON inválido: " . json_last_error_msg() . "\n";
}
