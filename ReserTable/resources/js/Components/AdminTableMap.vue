<script setup>
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import interact from 'interactjs';
import { useForm, router } from '@inertiajs/vue3';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';

const { t } = useI18n();

const props = defineProps({
    tables: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['table-created', 'table-updated']);

// Inicializar notificaciones
const { tableUpdated, tableUpdateError, tableCreated, tableDeleted, tableDeleteError, formError } = useNotifications();

const tables = ref([]);
const selectedTable = ref(null);
const showModal = ref(false);
const editingTable = ref(null);
const addingMode = ref(false);
const pendingPosition = ref(null);
const isDragging = ref(false); // Variable para detectar drag and drop

const mapWidth = 900;
const mapHeight = 600;

// Detectar modo oscuro
const isDarkMode = ref(false);

// Función para actualizar el estado del modo oscuro
const updateDarkMode = () => {
    isDarkMode.value = document.documentElement.classList.contains('dark');
};

// Observador para cambios en el modo oscuro
const observeDarkMode = () => {
    const observer = new MutationObserver(() => {
        updateDarkMode();
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
    
    return observer;
};

// Formulario para crear/editar mesa
const form = useForm({
    table_number: '',
    capacity: 2,
    status: 'available',
    position_x: 0,
    position_y: 0,
});

// Formulario específico para actualizar posiciones
const positionForm = useForm({
    table_number: '',
    capacity: 2,
    status: 'available',
    position_x: 0,
    position_y: 0,
});

const statusOptions = computed(() => [
    { label: t('admin.tables.status_available'), value: 'available' },
    { label: t('admin.tables.status_reserved'), value: 'reserved' },
    { label: t('admin.tables.status_occupied'), value: 'occupied' }
]);

// Función para validar y corregir posiciones
const validatePosition = (x, y) => {
    // Verificar que sean números válidos
    const numX = Number(x);
    const numY = Number(y);
    
    // Si no son números válidos o están fuera del rango permitido (5% - 95%)
    if (isNaN(numX) || isNaN(numY) || numX < 5 || numX > 95 || numY < 5 || numY > 95) {
        console.warn(`Posición inválida detectada: x=${x}, y=${y}. Usando posición central.`);
        return { x: 50, y: 50 }; // Posición central por defecto
    }
    
    return { x: numX, y: numY };
};

// Inicializar mesas desde props
const initializeTables = () => {
    tables.value = props.tables.map(table => {
        // Validar y corregir posiciones si es necesario
        const validPosition = validatePosition(table.position_x, table.position_y);
        
        return {
            id: table.id,
            x: validPosition.x,
            y: validPosition.y,
            status: table.status,
            capacity: table.capacity,
            table_number: table.table_number
        };
    });
};

// Inicializar componente
onMounted(async () => {
    initializeTables();
    updateDarkMode();
    observeDarkMode();
    await nextTick();
    
    // Esperar un poco más para asegurar que el DOM esté completamente renderizado
    setTimeout(() => {
        setupDragAndDrop();
    }, 200);
});

// Watch para actualizar cuando cambien las props
watch(() => props.tables, () => {
    initializeTables();
}, { deep: true });

// Watch para reconfigurar drag and drop cuando cambien las mesas
watch(tables, async () => {
    await nextTick();
    setTimeout(() => {
        setupDragAndDrop();
    }, 100);
}, { deep: true });

// Configurar funcionalidad de arrastrar y soltar
const setupDragAndDrop = () => {
    // Limpiar cualquier interacción previa
    interact('.table-marker.draggable').unset();
    
    // Esperar un momento para que el DOM se actualice
    setTimeout(() => {
        const draggableElements = document.querySelectorAll('.table-marker.draggable');
        console.log('Setting up drag for', draggableElements.length, 'elements');
        
        // Verificar que los elementos existen antes de configurar
        if (draggableElements.length === 0) {
            console.warn('No draggable elements found, retrying...');
            setTimeout(setupDragAndDrop, 200);
            return;
        }
          // Configurar cada elemento individualmente para debugging
        draggableElements.forEach((element, index) => {
            console.log(`Setting up drag for element ${index}:`, element.dataset.id);
            
            // Asegurar que el elemento tenga los atributos correctos
            if (!element.hasAttribute('data-x')) {
                element.setAttribute('data-x', '0');
            }
            if (!element.hasAttribute('data-y')) {
                element.setAttribute('data-y', '0');
            }
        });

        interact('.table-marker.draggable')
            .draggable({
                inertia: false, // Deshabilitar inercia temporalmente para debugging
                modifiers: [
                    interact.modifiers.restrictRect({
                        restriction: '.restaurant-map',
                        endOnly: false
                    })
                ],
                autoScroll: false, // Deshabilitar temporalmente
                listeners: {
                    start(event) {
                        const target = event.target.closest('.table-marker');
                        console.log('Drag START for:', target.dataset.id);
                        target.style.zIndex = '100';
                        target.classList.add('dragging');
                        target.style.transition = 'none';
                        isDragging.value = true; // Marcar que comenzó el drag
                    },
                    move(event) {
                        const target = event.target.closest('.table-marker');
                        
                        // Obtener el desplazamiento acumulado
                        const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                        const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
                        
                        console.log('Drag MOVE for:', target.dataset.id, 'dx:', event.dx, 'dy:', event.dy, 'x:', x, 'y:', y);
                        
                        // Aplicar la transformación inmediatamente
                        target.style.transform = `translate(${x}px, ${y}px)`;
                        
                        // Actualizar los atributos
                        target.setAttribute('data-x', x);
                        target.setAttribute('data-y', y);
                    },async end(event) {
                        const target = event.target.closest('.table-marker');
                        target.style.zIndex = '';
                        target.classList.remove('dragging');
                        
                        const container = document.querySelector('.restaurant-map');
                        const containerRect = container.getBoundingClientRect();
                        
                        // Obtener las coordenadas del transform actual
                        const translateX = parseFloat(target.getAttribute('data-x')) || 0;
                        const translateY = parseFloat(target.getAttribute('data-y')) || 0;
                        
                        // Obtener la posición inicial del elemento (sin transform)
                        const tableId = target.dataset.id;
                        const table = tables.value.find(t => t.id == tableId);
                        const initialX = (table.x / 100) * containerRect.width - 35; // 35px es la mitad del ancho de la mesa
                        const initialY = (table.y / 100) * containerRect.height - 35; // 35px es la mitad del alto de la mesa
                        
                        // Calcular la nueva posición absoluta
                        const finalX = initialX + translateX + 35; // +35 para obtener el centro
                        const finalY = initialY + translateY + 35; // +35 para obtener el centro
                        
                        // Convertir a porcentajes con validación estricta
                        let newX = Math.round((finalX / containerRect.width) * 100);
                        let newY = Math.round((finalY / containerRect.height) * 100);
                        
                        // Aplicar validación y corrección de posición
                        const validPosition = validatePosition(newX, newY);
                        newX = validPosition.x;
                        newY = validPosition.y;
                        
                        console.log('Position calculation:', {
                            tableId,
                            translateX,
                            translateY,
                            initialX,
                            initialY,
                            finalX,
                            finalY,
                            newX,
                            newY,
                            containerWidth: containerRect.width,
                            containerHeight: containerRect.height
                        });
                        
                        // Actualizar en la base de datos usando Inertia
                        try {
                            if (!table) {
                                throw new Error('Mesa no encontrada');
                            }
                            
                            // Usar Inertia para actualizar la posición con todos los datos necesarios
                            positionForm.table_number = table.table_number || table.id;
                            positionForm.capacity = table.capacity;
                            positionForm.status = table.status;
                            positionForm.position_x = newX;  // Nueva posición X
                            positionForm.position_y = newY;  // Nueva posición Y
                            
                            console.log('Sending position update:', {
                                tableId,
                                table_number: positionForm.table_number,
                                capacity: positionForm.capacity,
                                status: positionForm.status,
                                position_x: positionForm.position_x,
                                position_y: positionForm.position_y
                            });
                            
                            await new Promise((resolve, reject) => {
                                positionForm.put(`/admin/tables/${tableId}`, {
                                    preserveScroll: true,
                                    preserveState: true,
                                    onSuccess: () => {
                                        console.log('Position updated successfully');
                                        // Actualizar la posición local
                                        const table = tables.value.find(t => t.id == tableId);
                                        if (table) {
                                            table.x = newX;
                                            table.y = newY;
                                        }
                                        
                                        // Mostrar notificación de éxito
                                        tableUpdated(table.table_number || table.id);
                                        
                                        emit('table-updated');
                                        resolve();
                                    },
                                    onError: (errors) => {
                                        console.error('Error updating table position:', errors);
                                        
                                        // Mostrar notificación de error
                                        tableUpdateError();
                                        
                                        reject(errors);
                                    }
                                });
                            });
                              // Resetear transform con transición suave
                            target.style.transition = 'transform 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                            target.style.transform = '';
                            target.setAttribute('data-x', 0);
                            target.setAttribute('data-y', 0);
                            
                            // Remover la transición después de que termine
                            setTimeout(() => {
                                target.style.transition = '';
                            }, 300);
                            
                        } catch (error) {
                            console.error('Error updating table position:', error);
                            // Revertir posición en caso de error con transición suave
                            target.style.transition = 'transform 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                            target.style.transform = '';
                            target.setAttribute('data-x', 0);
                            target.setAttribute('data-y', 0);
                            setTimeout(() => {
                                target.style.transition = '';
                            }, 300);
                            alert(t('admin.tables.error_position_update'));
                        }
                        
                        // Pequeño delay antes de permitir clicks nuevamente
                        setTimeout(() => {
                            isDragging.value = false;
                        }, 150);
                    }
                }
            });
        
        console.log('Drag and drop configured for', draggableElements.length, 'tables');
    }, 100);
};

// Activar modo de añadir mesa
const enableAddMode = () => {
    addingMode.value = true;
    pendingPosition.value = null;
};

// Desactivar modo de añadir mesa
const disableAddMode = () => {
    addingMode.value = false;
    pendingPosition.value = null;
};

// Manejar clic en el mapa
const handleMapClick = (event) => {
    if (!addingMode.value) return;
    
    const container = event.currentTarget;
    const rect = container.getBoundingClientRect();
    let x = Math.round(((event.clientX - rect.left) / rect.width) * 100);
    let y = Math.round(((event.clientY - rect.top) / rect.height) * 100);
    
    // Validar y corregir la posición antes de continuar
    const validPosition = validatePosition(x, y);
    x = validPosition.x;
    y = validPosition.y;
    
    // Verificar que no haya conflicto con mesas existentes
    const conflict = tables.value.some(table => {
        const distance = Math.sqrt(Math.pow(table.x - x, 2) + Math.pow(table.y - y, 2));
        return distance < 15; // Distancia mínima entre mesas
    });
    
    if (conflict) {
        alert(t('admin.tables.position_conflict'));
        return;
    }
    
    pendingPosition.value = { x, y };
    
    // Abrir modal con la posición ya establecida
    editingTable.value = null;
    form.reset();
    form.position_x = x;
    form.position_y = y;
    form.table_number = getNextTableNumber();
    form.capacity = 2;
    form.status = 'available';
    showModal.value = true;
};

// Obtener el siguiente número de mesa disponible
const getNextTableNumber = () => {
    const tableNumbers = tables.value.map(table => table.table_number || table.id);
    let nextNumber = 1;
    while (tableNumbers.includes(nextNumber)) {
        nextNumber++;
    }
    return nextNumber;
};

// Editar mesa existente
const editTable = (table) => {
    editingTable.value = table;
    
    // Validar posiciones antes de cargar en el formulario
    const validPosition = validatePosition(table.x, table.y);
    
    form.table_number = table.table_number || table.id;
    form.capacity = table.capacity;
    form.status = table.status;
    form.position_x = validPosition.x;
    form.position_y = validPosition.y;
    showModal.value = true;
};

// Manejar click en mesa (solo si no se hizo drag)
const handleTableClick = (table, event) => {
    // Prevenir si acabamos de hacer drag
    if (isDragging.value) {
        console.log('Click prevented: table was being dragged');
        event.preventDefault();
        event.stopPropagation();
        return;
    }
    
    // Solo abrir modal si no hubo drag
    console.log('Opening edit modal for table:', table.id);
    editTable(table);
};

// Cerrar modal
const closeModal = () => {
    showModal.value = false;
    editingTable.value = null;
    form.reset();
    pendingPosition.value = null;
    disableAddMode();
};

// Enviar formulario
const submitTable = () => {
    // Validar y corregir posiciones antes de enviar
    const validPosition = validatePosition(form.position_x, form.position_y);
    form.position_x = validPosition.x;
    form.position_y = validPosition.y;
    
    if (editingTable.value) {
        // Actualizar mesa existente
        form.put(`/admin/tables/${editingTable.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                emit('table-updated');
                closeModal();
                
                // Reconfigurar drag and drop después de editar mesa
                nextTick(() => {
                    setTimeout(() => {
                        setupDragAndDrop();
                    }, 300);
                });
            },
            onError: (errors) => {
                console.error('Error updating table:', errors);
                formError(t('admin.tables.error_updating_table'));
            }
        });
    } else {
        // Crear nueva mesa
        form.post('/admin/tables', {
            preserveScroll: true,
            onSuccess: () => {
                tableCreated(form.table_number);
                emit('table-created');
                closeModal();
                
                // Reconfigurar drag and drop después de crear mesa
                nextTick(() => {
                    setTimeout(() => {
                        setupDragAndDrop();
                    }, 300);
                });
            },
            onError: (errors) => {
                console.error('Error creating table:', errors);
                formError(t('admin.tables.error_creating_table'));
            }
        });
    }
};

// Eliminar mesa
const deleteTable = async (table) => {
    if (!confirm(t('admin.tables.confirm_delete_table', { tableNumber: table.table_number || table.id }))) {
        return;
    }
    
    try {
        // Usar router de Inertia para eliminar
        router.delete(`/admin/tables/${table.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                tableDeleted(table.table_number || table.id);
                emit('table-updated');
                
                // Reconfigurar drag and drop
                nextTick(() => {
                    setTimeout(() => {
                        setupDragAndDrop();
                    }, 300);
                });
            },
            onError: (errors) => {
                console.error('Error deleting table:', errors);
                tableDeleteError();
            }
        });
    } catch (error) {
        console.error('Error deleting table:', error);
        tableDeleteError();
    }
};

// Colores según estado
const tableColor = (status) => {
    switch(status) {
        case 'available': return '#22C55E'; // Verde
        case 'occupied': return '#EF4444';  // Rojo
        case 'reserved': return '#F59E0B';  // Amarillo/Naranja
        default: return '#9CA3AF';          // Gris
    }
};

// Patas de las mesas en SVG
const tableLegs = [
    { x: 40, y: 170, width: 30, height: 30 },
    { x: 130, y: 170, width: 30, height: 30 },
];
</script>

<template>
    <div class="admin-table-map">
        <!-- Toolbar -->
        <div class="map-toolbar mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ t('admin.tables.interactive_table_map') }}</h5>
                    <p class="text-muted mb-0 small">
                        <span v-if="!addingMode">{{ t('admin.tables.drag_to_reposition') }}</span>
                        <span v-else class="text-primary">{{ t('admin.tables.click_to_place') }}</span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <Button 
                        v-if="!addingMode"
                        :label="t('admin.tables.add_table')" 
                        icon="pi pi-plus" 
                        class="p-button-success p-button-sm"
                        @click="enableAddMode"
                    />
                    <Button 
                        v-else
                        :label="t('common.cancel')" 
                        icon="pi pi-times" 
                        class="p-button-secondary p-button-sm"
                        @click="disableAddMode"
                    />
                </div>
            </div>
        </div>        <!-- Leyenda -->
        <div class="map-legend mb-3 p-2 border rounded">
            <div class="d-flex gap-4 small">
                <div class="d-flex align-items-center">
                    <div class="legend-color me-1" style="background-color: #22C55E !important;"></div>
                    {{ t('admin.tables.available') }}
                </div>
                <div class="d-flex align-items-center">
                    <div class="legend-color me-1" style="background-color: #F59E0B !important;"></div>
                    {{ t('admin.tables.reserved') }}
                </div>
                <div class="d-flex align-items-center">
                    <div class="legend-color me-1" style="background-color: #EF4444 !important;"></div>
                    {{ t('admin.tables.occupied') }}
                </div>
                <div class="d-flex align-items-center">
                    <div class="legend-color me-1" style="background-color: #9CA3AF !important;"></div>
                    {{ t('admin.tables.inactive') }}
                </div>
            </div>
        </div><!-- Mapa -->
        <div class="map-container">            <div
                class="restaurant-map position-relative"
                :class="{ 
                    'adding-mode': addingMode,
                    'dark-mode': isDarkMode
                }"
                :style="{
                    backgroundImage: 'url(/images/restaurant-parquet-bg.svg)',
                    backgroundSize: 'cover',
                    backgroundPosition: 'center',
                    backgroundRepeat: 'no-repeat',
                    width: mapWidth + 'px',
                    height: mapHeight + 'px',
                    borderRadius: '12px',
                    boxShadow: 'var(--box-shadow)',
                    border: addingMode ? '2px dashed var(--primary-color)' : '1px solid var(--surface-border)'
                }"
                @click="handleMapClick"
            >
                <!-- Posición pendiente -->
                <div
                    v-if="pendingPosition"
                    class="pending-table position-absolute"
                    :style="{
                        left: `calc(${pendingPosition.x}% - 35px)`,
                        top: `calc(${pendingPosition.y}% - 35px)`,
                        width: '70px',
                        height: '70px',
                    }"
                >
                    <div class="pending-marker">
                        <i class="pi pi-plus"></i>
                    </div>
                </div>                <!-- Mesas existentes -->
                <div
                    v-for="table in tables"
                    :key="table.id"
                    class="table-marker position-absolute draggable"
                    :data-id="table.id"
                    data-x="0"
                    data-y="0"
                    :style="{
                        left: `calc(${table.x}% - 35px)`,
                        top: `calc(${table.y}% - 35px)`,
                        width: '70px',
                        height: '70px',
                        zIndex: selectedTable?.id === table.id ? 10 : 5
                    }"
                    @click.stop="handleTableClick(table, $event)"
                    @contextmenu.prevent="deleteTable(table)"
                >
                    <!-- SVG de la mesa -->
                    <svg
                        viewBox="0 0 200 200"
                        class="table-svg"
                        :class="table.status"
                    >
                        <g>
                            <!-- Tablero de la mesa -->
                            <rect
                                x="25"
                                y="25"
                                width="150"
                                height="150"
                                rx="15"
                                ry="15"
                                :fill="tableColor(table.status)"
                                stroke="var(--surface-600)"
                                stroke-width="3"
                            />
                            <!-- Patas de la mesa -->
                            <rect
                                v-for="(leg, index) in tableLegs"
                                :key="index"
                                :x="leg.x"
                                :y="leg.y"
                                :width="leg.width"
                                :height="leg.height"
                                rx="8"
                                ry="8"
                                fill="var(--surface-700)"
                            />
                        </g>
                    </svg>

                    <!-- Información de la mesa -->
                    <div class="table-info">
                        <div class="table-number">{{ table.table_number || table.id }}</div>
                        <div class="table-capacity">{{ table.capacity }} {{ t('admin.tables.people') }}</div>
                    </div>

                    <!-- Botón de eliminar (visible en hover) -->
                    <div class="table-actions">
                        <button 
                            class="btn btn-danger btn-sm table-delete-btn"
                            @click.stop="deleteTable(table)"
                            :title="t('admin.tables.right_click_or_button')"
                        >
                            <i class="pi pi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Instrucciones -->
            <div class="map-instructions mt-2">
                <small class="text-muted">
                    <strong>{{ t('admin.tables.instructions') }}:</strong> 
                    {{ t('admin.tables.drag_move') }} • {{ t('admin.tables.click_edit') }} • {{ t('admin.tables.right_click_delete') }} • 
                    <span v-if="addingMode" class="text-primary">{{ t('admin.tables.click_empty_area') }}</span>
                </small>
            </div>
        </div>

        <!-- Modal para crear/editar mesa -->
        <Dialog 
            v-model:visible="showModal" 
            :header="editingTable ? t('admin.tables.edit_table') : t('admin.tables.new_table')" 
            :modal="true" 
            :closable="true" 
            :style="{ width: '500px' }"
            class="admin-table-dialog"
        >
            <form @submit.prevent="submitTable" class="row g-3">
                <div class="col-12">
                    <label class="form-label">{{ t('admin.tables.table_number') }}</label>
                    <InputText 
                        v-model="form.table_number" 
                        class="w-100" 
                        type="number"
                        required 
                        :min="1"
                    />
                </div>
                <div class="col-12">
                    <label class="form-label">{{ t('admin.tables.capacity') }}</label>
                    <InputText 
                        v-model="form.capacity" 
                        class="w-100" 
                        type="number"
                        required 
                        :min="1"
                        :max="20"
                    />
                </div>
                <div class="col-12">
                    <label class="form-label">{{ t('common.status') }}</label>
                    <Dropdown 
                        v-model="form.status" 
                        :options="statusOptions" 
                        optionLabel="label" 
                        optionValue="value" 
                        class="w-100" 
                    />
                </div>
                <div class="col-6">
                    <label class="form-label">{{ t('admin.tables.position_x') }} (%)</label>
                    <InputText 
                        v-model="form.position_x" 
                        class="w-100" 
                        type="number"
                        :min="0"
                        :max="100"
                        readonly
                    />
                </div>
                <div class="col-6">
                    <label class="form-label">{{ t('admin.tables.position_y') }} (%)</label>
                    <InputText 
                        v-model="form.position_y" 
                        class="w-100" 
                        type="number"
                        :min="0"
                        :max="100"
                        readonly
                    />
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                    <Button 
                        :label="t('common.cancel')" 
                        icon="pi pi-times" 
                        class="p-button-text" 
                        @click="closeModal" 
                        type="button" 
                    />
                    <Button 
                        :label="editingTable ? t('common.update') : t('common.create')" 
                        icon="pi pi-check" 
                        type="submit"
                        :loading="form.processing"
                    />
                </div>
            </form>
        </Dialog>
    </div>
</template>

<style scoped>
.admin-table-map {
    width: 100%;
}

.map-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.restaurant-map {
    cursor: default;
    transition: border-color 0.3s ease;
    background-blend-mode: normal;
    position: relative;
    overflow: hidden;
}

.restaurant-map::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: transparent;
    transition: background-color 0.3s ease;
    pointer-events: none;
    z-index: 1;
}

/* Overlay sutil para modo oscuro */
:global(.dark) .restaurant-map::before {
    background: rgba(0, 0, 0, 0.3);
}

/* Soporte para clase dark en el elemento padre */
.restaurant-map.dark-mode::before {
    background: rgba(0, 0, 0, 0.3);
}

/* Mejorar contraste de las mesas en el fondo de parquet */
.table-marker .table-svg {
    filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.3));
}

.table-marker:hover .table-svg {
    filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.4));
}

/* Mejorar la información de las mesas para mejor legibilidad */
.table-info {
    position: absolute;
    bottom: -30px;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    color: var(--text-color);
    font-weight: 600;
    white-space: nowrap;
    font-size: 0.75rem;
    background: rgba(255, 255, 255, 0.95);
    padding: 3px 8px;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(5px);
}

/* Modo oscuro para la información de las mesas */
:global(.dark) .table-info {
    background: rgba(30, 30, 30, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
}

.restaurant-map.adding-mode {
    cursor: crosshair;
}

.table-marker {
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
    z-index: 10;
    position: absolute !important; /* Forzar posición absoluta */
    will-change: transform;
}

.table-marker.draggable {
    cursor: grab;
    touch-action: none;
    pointer-events: auto;
}

/* Importante: Sin transición durante el drag */
.table-marker.dragging {
    cursor: grabbing !important;
    z-index: 100 !important;
    transition: none !important; /* Crítico: sin transiciones durante drag */
    transform-origin: center center;
    pointer-events: auto;
}

.table-marker.draggable:hover:not(.dragging) {
    transform: scale(1.02);
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.1));
}

.table-marker:hover:not(.dragging) {
    transform: scale(1.02);
    z-index: 15 !important;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.1));
}

.table-svg {
    width: 100%;
    height: 100%;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
    will-change: filter;
}

.table-marker:hover:not(.dragging) .table-svg {
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.15));
}

.table-marker.dragging .table-svg {
    filter: drop-shadow(0 8px 25px rgba(0, 0, 0, 0.2));
}

.table-info {
    position: absolute;
    bottom: -30px;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    color: var(--text-color);
    font-weight: 600;
    white-space: nowrap;
    font-size: 0.75rem;
    background: var(--surface-0);
    padding: 2px 6px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.table-number {
    font-size: 0.8rem;
    margin-bottom: 1px;
}

.table-capacity {
    font-size: 0.7rem;
    color: var(--text-color-secondary);
}

.table-actions {
    position: absolute;
    top: -8px;
    right: -8px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.table-marker:hover .table-actions {
    opacity: 1;
}

.table-delete-btn {
    width: 24px;
    height: 24px;
    padding: 0;
    font-size: 0.7rem;
    border-radius: 50%;
}

.pending-table {
    z-index: 20;
    position: relative;
}

.pending-marker {
    width: 70px;
    height: 70px;
    border: 3px dashed var(--primary-color);
    border-radius: 12px;
    background: rgba(var(--primary-color-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 1.5rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
    100% {
        opacity: 1;
    }
}

.legend-color {
    width: 16px !important;
    height: 16px !important;
    border-radius: 3px;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    display: inline-block !important;
    flex-shrink: 0;
    min-width: 16px;
    min-height: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.map-legend {
    background: var(--surface-card);
    border: 1px solid var(--surface-border) !important;
    border-radius: 8px;
}

.map-legend .d-flex.align-items-center {
    margin-bottom: 0;
}

.map-toolbar {
    background: var(--surface-card);
    border: 1px solid var(--surface-border);
    border-radius: 8px;
    padding: 1rem;
}

.map-legend {
    background: var(--surface-50);
    border-color: var(--surface-border) !important;
}

.map-instructions {
    max-width: 600px;
    text-align: center;
}

/* Modo oscuro */
@media (prefers-color-scheme: dark) {
    .restaurant-map {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
    }
    
    .table-info {
        background: var(--surface-800);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .restaurant-map {
        width: 100% !important;
        max-width: 600px;
        height: 400px !important;
    }
    
    .map-toolbar {
        padding: 0.75rem;
    }
    
    .map-toolbar h5 {
        font-size: 1rem;
    }
    
    .table-marker {
        width: 50px !important;
        height: 50px !important;
    }
    
    .table-marker {
        left: calc(var(--x) - 25px) !important;
        top: calc(var(--y) - 25px) !important;
    }
}

/* Estilos personalizados para Toast - Mejor legibilidad */
:global(.p-toast) {
    z-index: 1100 !important;
}

:global(.p-toast .p-toast-message) {
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    background: rgba(255, 255, 255, 0.95) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2) !important;
    border-radius: 12px !important;
}

/* Toast en modo oscuro */
:global(.dark .p-toast .p-toast-message) {
    background: rgba(30, 30, 30, 0.95) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4) !important;
}

/* Mejorar contraste del texto del Toast */
:global(.p-toast .p-toast-message .p-toast-message-text) {
    color: var(--text-color) !important;
    font-weight: 500 !important;
}

:global(.p-toast .p-toast-message .p-toast-summary) {
    font-weight: 600 !important;
    color: var(--text-color) !important;
}

:global(.p-toast .p-toast-message .p-toast-detail) {
    color: var(--text-color-secondary) !important;
    opacity: 0.9 !important;
}

/* Toast de éxito */
:global(.p-toast .p-toast-message.p-toast-message-success) {
    background: rgba(34, 197, 94, 0.15) !important;
    backdrop-filter: blur(20px) !important;
    border: 1px solid rgba(34, 197, 94, 0.3) !important;
}

:global(.p-toast .p-toast-message.p-toast-message-success .p-toast-message-icon) {
    color: #16a34a !important;
}

/* Toast de error */
:global(.p-toast .p-toast-message.p-toast-message-error) {
    background: rgba(239, 68, 68, 0.15) !important;
    backdrop-filter: blur(20px) !important;
    border: 1px solid rgba(239, 68, 68, 0.3) !important;
}

:global(.p-toast .p-toast-message.p-toast-message-error .p-toast-message-icon) {
    color: #dc2626 !important;
}

/* Animación suave para el Toast */
:global(.p-toast .p-toast-message) {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

:global(.p-toast .p-toast-message:hover) {
    transform: translateY(-2px) !important;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3) !important;
}
</style>
