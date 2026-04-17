<script setup lang="ts">
import { ref, onMounted } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';

// Dados mockados para teste visual imediato
const products = ref([
    { id: 1, name: 'Placa de Vídeo RTX 4060', category: 'Hardware', price: 2500, stock: 10 },
    { id: 2, name: 'Monitor Gamer 144hz', category: 'Periféricos', price: 1200, stock: 5 },
    { id: 3, name: 'Teclado Mecânico RGB', category: 'Periféricos', price: 350, stock: 15 },
]);

const filters = ref({ global: { value: null } });
const loading = ref(false);
</script>

<template>
    <div class="p-6 bg-white rounded-xl shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Gerenciamento de Produtos</h1>
            
            <div class="flex gap-3">
                <IconField>
                    <InputIcon class="pi pi-search" />
                    <InputText v-model="filters.global.value" placeholder="Buscar produto..." />
                </IconField>
                <Button label="Novo Produto" icon="pi pi-plus" severity="success" raised />
            </div>
        </div>

        <DataTable 
            :value="products" 
            paginator :rows="5" 
            :loading="loading"
            tableStyle="min-width: 50rem"
            class="p-datatable-sm"
        >
            <template #empty> Nenhum produto encontrado. </template>
            
            <Column field="id" header="ID" sortable style="width: 10%"></Column>
            <Column field="name" header="Nome" sortable style="width: 40%"></Column>
            <Column field="category" header="Categoria" sortable style="width: 20%"></Column>
            <Column field="price" header="Preço" sortable style="width: 15%">
                <template #body="slotProps">
                    {{ new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(slotProps.data.price) }}
                </template>
            </Column>
            <Column header="Ações" style="width: 15%">
                <template #body>
                    <div class="flex gap-2">
                        <Button icon="pi pi-pencil" severity="warn" text rounded />
                        <Button icon="pi pi-trash" severity="danger" text rounded />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>