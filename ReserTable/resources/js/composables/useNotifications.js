import { useToast } from 'primevue/usetoast';
import { useI18n } from 'vue-i18n';

// Composable para notificaciones globales
export const useNotifications = () => {
    const toast = useToast();
    const { t } = useI18n();

    const showSuccess = (message, title = null, life = 3000) => {
        toast.add({
            severity: 'success',
            summary: title || t('notifications.success'),
            detail: message,
            life: life
        });
    };

    const showError = (message, title = null, life = 5000) => {
        toast.add({
            severity: 'error',
            summary: title || t('notifications.error'),
            detail: message,
            life: life
        });
    };

    const showWarning = (message, title = null, life = 4000) => {
        toast.add({
            severity: 'warn',
            summary: title || t('notifications.warning'),
            detail: message,
            life: life
        });
    };

    const showInfo = (message, title = null, life = 3000) => {
        toast.add({
            severity: 'info',
            summary: title || t('notifications.info'),
            detail: message,
            life: life
        });
    };    // Funciones específicas para el contexto de mesas
    const tableUpdated = (tableNumber) => {
        showSuccess(
            t('notifications.tables.table_repositioned', { tableNumber }),
            t('notifications.tables.position_updated')
        );
    };

    const tableUpdateError = () => {
        showError(
            t('notifications.tables.position_update_failed'),
            t('notifications.tables.update_error')
        );
    };

    const tableCreated = (tableNumber) => {
        showSuccess(
            t('notifications.tables.table_created_success', { tableNumber }),
            t('notifications.tables.table_created')
        );
    };

    const tableDeleted = (tableNumber) => {
        showSuccess(
            t('notifications.tables.table_deleted_success', { tableNumber }),
            t('notifications.tables.table_deleted')
        );
    };

    const tableDeleteError = () => {
        showError(
            t('notifications.tables.delete_failed'),
            t('notifications.tables.delete_error')
        );
    };    // Funciones específicas para reservas
    const reservationCreated = () => {
        showSuccess(
            t('notifications.reservations.created_success'),
            t('notifications.reservations.confirmed')
        );
    };

    const reservationUpdated = () => {
        showSuccess(
            t('notifications.reservations.updated_success'),
            t('notifications.reservations.updated')
        );
    };

    const reservationCancelled = () => {
        showInfo(
            t('notifications.reservations.cancelled_info'),
            t('notifications.reservations.cancelled')
        );
    };

    const reservationError = (message = null) => {
        showError(
            message || t('notifications.reservations.processing_error'), 
            t('notifications.reservations.error')
        );
    };    // Funciones específicas para el menú
    const menuItemSaved = (itemName) => {
        showSuccess(
            t('notifications.menu.item_saved_success', { itemName }),
            t('notifications.menu.item_saved')
        );
    };

    const menuItemDeleted = (itemName) => {
        showSuccess(
            t('notifications.menu.item_deleted_success', { itemName }),
            t('notifications.menu.item_deleted')
        );
    };

    // Funciones para formularios generales
    const formSaved = (itemType = 'elemento') => {
        showSuccess(
            t('notifications.forms.item_saved', { itemType }),
            t('notifications.forms.saved_success')
        );
    };

    const formError = (message = null) => {
        showError(
            message || t('notifications.forms.check_data'), 
            t('notifications.forms.form_error')
        );
    };

    const validationError = (message = null) => {
        showWarning(
            message || t('notifications.forms.validation_errors'), 
            t('notifications.forms.incomplete_data')
        );
    };

    // Funciones para operaciones de red
    const networkError = () => {
        showError(
            t('notifications.network.connection_failed'),
            t('notifications.network.connection_error')
        );
    };

    const unauthorized = () => {
        showWarning(
            t('notifications.network.unauthorized'),
            t('notifications.network.session_expired')
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
