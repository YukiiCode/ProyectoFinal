import { useToast } from 'primevue/usetoast';

// Composable para notificaciones globales
export const useNotifications = () => {
    const toast = useToast();

    const showSuccess = (message, title = 'Éxito', life = 3000) => {
        toast.add({
            severity: 'success',
            summary: title,
            detail: message,
            life: life
        });
    };

    const showError = (message, title = 'Error', life = 5000) => {
        toast.add({
            severity: 'error',
            summary: title,
            detail: message,
            life: life
        });
    };

    const showWarning = (message, title = 'Advertencia', life = 4000) => {
        toast.add({
            severity: 'warn',
            summary: title,
            detail: message,
            life: life
        });
    };

    const showInfo = (message, title = 'Información', life = 3000) => {
        toast.add({
            severity: 'info',
            summary: title,
            detail: message,
            life: life
        });
    };

    // Funciones específicas para el contexto de mesas
    const tableUpdated = (tableNumber) => {
        showSuccess(
            `Mesa ${tableNumber} reposicionada correctamente`,
            'Posición actualizada'
        );
    };

    const tableUpdateError = () => {
        showError(
            'No se pudo actualizar la posición de la mesa',
            'Error al actualizar'
        );
    };

    const tableCreated = (tableNumber) => {
        showSuccess(
            `Mesa ${tableNumber} creada exitosamente`,
            'Mesa creada'
        );
    };

    const tableDeleted = (tableNumber) => {
        showSuccess(
            `Mesa ${tableNumber} eliminada correctamente`,
            'Mesa eliminada'
        );
    };

    const tableDeleteError = () => {
        showError(
            'No se pudo eliminar la mesa',
            'Error al eliminar'
        );
    };

    // Funciones específicas para reservas
    const reservationCreated = () => {
        showSuccess(
            'La reserva ha sido creada exitosamente',
            'Reserva confirmada'
        );
    };

    const reservationUpdated = () => {
        showSuccess(
            'La reserva ha sido actualizada correctamente',
            'Reserva actualizada'
        );
    };

    const reservationCancelled = () => {
        showInfo(
            'La reserva ha sido cancelada',
            'Reserva cancelada'
        );
    };

    const reservationError = (message = 'Error al procesar la reserva') => {
        showError(message, 'Error en reserva');
    };

    // Funciones específicas para el menú
    const menuItemSaved = (itemName) => {
        showSuccess(
            `${itemName} guardado correctamente`,
            'Elemento del menú guardado'
        );
    };

    const menuItemDeleted = (itemName) => {
        showSuccess(
            `${itemName} eliminado del menú`,
            'Elemento eliminado'
        );
    };

    // Funciones para formularios generales
    const formSaved = (itemType = 'elemento') => {
        showSuccess(
            `El ${itemType} ha sido guardado correctamente`,
            'Guardado exitoso'
        );
    };

    const formError = (message = 'Por favor, revisa los datos ingresados') => {
        showError(message, 'Error en formulario');
    };

    const validationError = (message = 'Hay errores en el formulario') => {
        showWarning(message, 'Datos incompletos');
    };

    // Funciones para operaciones de red
    const networkError = () => {
        showError(
            'No se pudo conectar con el servidor. Revisa tu conexión.',
            'Error de conexión'
        );
    };

    const unauthorized = () => {
        showWarning(
            'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
            'Sesión expirada'
        );
    };

    return {
        // Funciones básicas
        showSuccess,
        showError,
        showWarning,
        showInfo,
        
        // Funciones específicas para mesas
        tableUpdated,
        tableUpdateError,
        tableCreated,
        tableDeleted,
        tableDeleteError,
        
        // Funciones específicas para reservas
        reservationCreated,
        reservationUpdated,
        reservationCancelled,
        reservationError,
        
        // Funciones específicas para menú
        menuItemSaved,
        menuItemDeleted,
        
        // Funciones para formularios
        formSaved,
        formError,
        validationError,
        
        // Funciones para errores de red
        networkError,
        unauthorized
    };
};
