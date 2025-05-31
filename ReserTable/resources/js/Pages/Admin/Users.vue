<script setup>
import { ref } from 'vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';

const users = ref(usePage().props.users || []);
const showDialog = ref(false);
const editingUser = ref(null);
const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'employee',
});

function openNew() {
    editingUser.value = null;
    form.reset();
    form.role = 'employee';
    showDialog.value = true;
}
function editUser(user) {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.role = user.role;
    showDialog.value = true;
}
function hideDialog() {
    showDialog.value = false;
    editingUser.value = null;
    form.reset();
    form.role = 'employee';
}
function submit() {
    if (editingUser.value) {
        router.put(route('admin.users.update', editingUser.value.id), {
            name: form.name,
            email: form.email,
            role: form.role,
        });
    } else {
        router.post(route('admin.users.store'), form);
    }
    hideDialog();
}
function removeUser(user) {
    if (confirm('¿Seguro que quieres eliminar este usuario?')) {
        router.delete(route('admin.users.destroy', user.id));
    }
}
const roleOptions = [
    { label: 'Administrador', value: 'admin' },
    { label: 'Empleado', value: 'employee' }
];
</script>

<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">Gestión de Usuarios</h1>
                    <p class="text-muted mb-0">Administra los usuarios del sistema</p>
                </div>
                <Button label="Nuevo Usuario" icon="pi pi-plus" class="p-button-primary" @click="openNew" />
            </div>

            <!-- Users Table -->
            <div class="row">
                <div class="col-12">
                    <div class="admin-card">
                        <div class="admin-card-body">
                            <DataTable 
                                :value="users" 
                                responsive-layout="scroll" 
                                striped-rows 
                                paginator 
                                :rows="10" 
                                :rows-per-page-options="[5,10,20]"
                                class="p-datatable-sm"
                            >
                                <Column field="name" header="Nombre" sortable />
                                <Column field="email" header="Email" sortable />
                                <Column field="role" header="Rol" sortable>
                                    <template #body="{ data }">
                                        <span class="badge" :class="data.role === 'admin' ? 'bg-danger' : 'bg-primary'">
                                            {{ data.role === 'admin' ? 'Administrador' : 'Empleado' }}
                                        </span>
                                    </template>
                                </Column>
                                <Column header="Acciones" class="text-center" style="width: 120px">
                                    <template #body="{ data }">
                                        <Button 
                                            icon="pi pi-pencil" 
                                            class="p-button-rounded p-button-info p-button-sm me-2" 
                                            @click="() => editUser(data)"
                                            title="Editar usuario"
                                        />
                                        <Button 
                                            icon="pi pi-trash" 
                                            class="p-button-rounded p-button-danger p-button-sm" 
                                            @click="() => removeUser(data)"
                                            title="Eliminar usuario"
                                        />
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dialog for Create/Edit User -->
            <Dialog 
                v-model:visible="showDialog" 
                :header="editingUser ? 'Editar Usuario' : 'Nuevo Usuario'" 
                :modal="true" 
                :closable="true" 
                :style="{ width: '450px' }"
                class="p-fluid"
            >
                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nombre</label>
                        <InputText v-model="form.name" class="w-100" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email</label>
                        <InputText v-model="form.email" type="email" class="w-100" required />
                    </div>
                    <div class="mb-3" v-if="!editingUser">
                        <label class="form-label fw-medium">Contraseña</label>
                        <InputText v-model="form.password" type="password" class="w-100" required />
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-medium">Rol</label>
                        <Dropdown 
                            v-model="form.role" 
                            :options="roleOptions" 
                            optionLabel="label" 
                            optionValue="value" 
                            class="w-100" 
                        />
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <Button 
                            label="Cancelar" 
                            icon="pi pi-times" 
                            class="p-button-text" 
                            @click="hideDialog" 
                            type="button" 
                        />
                        <Button 
                            :label="editingUser ? 'Actualizar' : 'Crear'" 
                            icon="pi pi-check" 
                            type="submit"
                            :loading="form.processing"
                        />
                    </div>
                </form>
            </Dialog>
        </div>
    </AdminLayout>
</template>
