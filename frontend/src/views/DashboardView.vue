<template>
  <div class="min-h-screen bg-[#11062b] font-sans text-slate-100">
    
    <nav class="bg-[#1a0b3b]/50 backdrop-blur-md border-b border-[#251052] px-6 py-3 flex justify-between items-center shadow-lg">
      <div class="flex items-center gap-2">
        <i class="pi pi-box text-violet-400 text-2xl"></i>
        <span class="text-xl font-bold tracking-tight text-white">Innyx Inventory</span>
      </div>

      <div class="flex items-center gap-4">
        <div class="text-right border-r border-[#251052] pr-4">
          <p class="text-xs text-violet-300 uppercase tracking-widest">Usuário Logado</p>
          <p class="font-bold text-white">{{ userName }}</p>
        </div>
        <button @click="handleLogout" class="p-2 rounded-full border border-red-500/50 text-red-500 hover:bg-red-500/10 transition-colors">
          <i class="pi pi-power-off"></i>
        </button>
      </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto">
      <header class="flex justify-between items-end mb-10">
        <div>
          <h2 class="text-4xl font-black text-white mb-2">Produtos</h2>
          <p class="text-violet-300 text-lg">Controle total do seu estoque.</p>
        </div>
        <Button label="Adicionar Novo" icon="pi pi-plus" raised severity="primary" class="px-6 py-3" @click="showModal = true" />
      </header>

      <div class="bg-[#1a0b3b]/30 border border-[#251052] rounded-2xl shadow-2xl backdrop-blur-sm overflow-hidden">
        
        <div v-if="loading" class="p-24 text-center">
          <div class="inline-flex items-center justify-center w-20 h-20 bg-[#251052]/50 rounded-full mb-6">
            <i class="pi pi-spin pi-spinner text-3xl text-violet-400"></i>
          </div>
          <p class="text-xl text-violet-200 font-medium">Sincronizando banco de dados...</p>
        </div>

        <div v-else class="p-6">
          <DataTable :value="products" responsiveLayout="scroll" :pt="{ root: { class: 'custom-table' } }">
            <Column header="Foto" style="width: 5%">
              <template #body="slotProps">
                <img v-if="slotProps.data.image" :src="`http://localhost/storage/${slotProps.data.image}`" class="w-12 h-12 object-cover rounded border border-[#251052]" />
                <div v-else class="w-12 h-12 bg-[#251052]/50 rounded border border-[#251052] flex items-center justify-center">
                  <i class="pi pi-image text-violet-400"></i>
                </div>
              </template>
            </Column>

            <Column field="name" header="Produto">
              <template #body="slotProps">
                <div class="font-bold text-white">{{ slotProps.data.name }}</div>
                <div class="text-sm text-violet-300">{{ slotProps.data.description }}</div>
              </template>
            </Column>

            <Column field="price" header="Preço">
              <template #body="slotProps">
                <span class="text-emerald-400 font-bold">R$ {{ slotProps.data.price }}</span>
              </template>
            </Column>

            <Column header="Ações" style="width: 10%; text-align: right">
              <template #body="slotProps">
                <div class="flex gap-2 justify-end">
                  <Button icon="pi pi-pencil" text rounded class="text-violet-400 hover:bg-[#251052]" />
                  <Button icon="pi pi-trash" text rounded class="text-red-400 hover:bg-red-900/30" />
                </div>
              </template>
            </Column>
            
            <template #empty>
              <div class="text-center p-8 text-violet-300">Nenhum produto cadastrado.</div>
            </template>
          </DataTable>
        </div>
      </div>

      <Dialog v-model:visible="showModal" modal header="Cadastrar Novo Produto" :style="{ width: '40rem' }" :pt="{ root: { class: 'custom-dialog' }, header: { class: 'custom-dialog-header' }, content: { class: 'custom-dialog-content' } }">
        
        <form @submit.prevent="submitProduct" class="flex flex-col gap-5 mt-4">
          
          <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-violet-300">Nome do Produto</label>
            <input v-model="form.name" type="text" required class="bg-[#11062b] border border-[#251052] rounded-lg p-3 text-white focus:outline-none focus:border-violet-500 transition-colors" placeholder="Ex: Teclado Mecânico" />
          </div>

          <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-violet-300">Descrição</label>
            <textarea v-model="form.description" required rows="3" class="bg-[#11062b] border border-[#251052] rounded-lg p-3 text-white focus:outline-none focus:border-violet-500 transition-colors" placeholder="Detalhes do produto..."></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-2">
              <label class="text-sm font-semibold text-violet-300">Preço (R$)</label>
              <input v-model="form.price" type="number" step="0.01" required class="bg-[#11062b] border border-[#251052] rounded-lg p-3 text-white focus:outline-none focus:border-violet-500 transition-colors" placeholder="199.90" />
            </div>

            <div class="flex flex-col gap-2">
              <label class="text-sm font-semibold text-violet-300">Data de Validade</label>
              <input v-model="form.expiration_date" type="date" required class="bg-[#11062b] border border-[#251052] rounded-lg p-3 text-white focus:outline-none focus:border-violet-500 transition-colors dark-calendar" />
            </div>
          </div>

          <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-violet-300">Foto do Produto (JPEG, PNG)</label>
            <input @change="handleFileUpload" type="file" accept="image/*" required class="bg-[#11062b] border border-[#251052] rounded-lg p-2 text-violet-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-violet-900 file:text-white hover:file:bg-violet-800 transition-colors" />
          </div>

          <div class="flex justify-end gap-3 mt-6 border-t border-[#251052] pt-4">
            <Button label="Cancelar" icon="pi pi-times" text class="text-violet-400 hover:bg-[#251052]" @click="showModal = false" type="button" />
            <Button label="Salvar Produto" icon="pi pi-check" raised severity="primary" type="submit" :loading="isSaving" />
          </div>

        </form>
      </Dialog>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import axios from 'axios';

const router = useRouter();
const userName = ref('...');
const products = ref([]);
const loading = ref(true);

const showModal = ref(false);
const isSaving = ref(false);
const form = ref({
  name: '',
  description: '',
  price: '',
  expiration_date: '',
  category_id: 1,
  image: null
});

const handleFileUpload = (event) => {
  form.value.image = event.target.files[0];
};

const fetchProducts = async () => {
  try {
    // AJUSTADO: Porta 80 (sem o :8000)
    const token = localStorage.getItem('token');
    const response = await axios.get('http://localhost/api/v1/products', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    products.value = response.data.data;
  } catch (error) {
    console.error('Erro ao buscar:', error);
  } finally {
    loading.value = false;
  }
};

const submitProduct = async () => {
  try {
    isSaving.value = true;
    const token = localStorage.getItem('token');
    
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('description', form.value.description);
    formData.append('price', form.value.price);
    formData.append('expiration_date', form.value.expiration_date);
    formData.append('category_id', 1);
    formData.append('image', form.value.image);

    // AJUSTADO: Porta 80 (sem o :8000)
    await axios.post('http://localhost/api/v1/products', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    showModal.value = false;
    form.value = { name: '', description: '', price: '', expiration_date: '', category_id: 1, image: null };
    
    loading.value = true;
    await fetchProducts();

  } catch (error) {
    console.error('Erro ao salvar:', error);
    alert('Erro ao salvar. Verifique o console.');
  } finally {
    isSaving.value = false;
  }
};

onMounted(() => {
  userName.value = localStorage.getItem('user_name') || 'Administrador';
  fetchProducts();
});

const handleLogout = async () => {
  try {
    const token = localStorage.getItem('token');
    await axios.post('http://localhost/api/v1/logout', {}, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
  } catch (error) {
    console.error('Erro ao deslogar:', error);
  } finally {
    localStorage.clear();
    router.push('/login');
  }
};
</script>

<style>
.custom-table .p-datatable-wrapper { background: transparent !important; }
.custom-table table { background: transparent !important; }
.custom-table .p-datatable-thead > tr > th { 
  background: rgba(37, 16, 82, 0.3) !important; 
  color: #a78bfa !important; 
  border-bottom: 1px solid #251052 !important; 
}
.custom-table .p-datatable-tbody > tr { background: transparent !important; color: #f1f5f9 !important; }
.custom-table .p-datatable-tbody > tr:hover { background: rgba(37, 16, 82, 0.5) !important; }
.custom-table .p-datatable-tbody > tr > td { border-bottom: 1px solid #251052 !important; }

.custom-dialog { background: #1a0b3b !important; border: 1px solid #251052 !important; border-radius: 1rem !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7) !important; }
.custom-dialog-header { background: transparent !important; color: white !important; border-bottom: 1px solid #251052 !important; padding: 1.5rem !important; border-top-left-radius: 1rem; border-top-right-radius: 1rem; }
.custom-dialog-content { background: transparent !important; color: #cbd5e1 !important; padding: 1.5rem !important; }
.p-dialog .p-dialog-header .p-dialog-title { font-weight: 800; font-size: 1.5rem; }
.p-dialog-header-icon { color: #a78bfa !important; }
.p-dialog-header-icon:hover { background: #251052 !important; }

::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }
</style>