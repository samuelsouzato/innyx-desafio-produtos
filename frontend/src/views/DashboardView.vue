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
          <p class="text-violet-300 text-lg">Gerenciamento completo de estoque.</p>
        </div>
        <Button label="Adicionar Novo" icon="pi pi-plus" raised class="!bg-emerald-500 !border-none px-6 py-3 font-bold" @click="openCreateModal" />
      </header>

      <div class="mb-6">
        <div class="relative w-full shadow-2xl">
          <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-violet-400"></i>
          <input 
            v-model="searchQuery" 
            @input="handleSearch"
            type="text" 
            placeholder="Buscar por nome ou descrição..." 
            class="w-full bg-[#1a0b3b]/50 border border-[#251052] rounded-xl py-4 pl-12 pr-4 text-white focus:border-violet-500 outline-none transition-all text-lg"
          />
        </div>
      </div>

      <div class="bg-[#1a0b3b]/30 border border-[#251052] rounded-2xl shadow-2xl backdrop-blur-sm overflow-hidden">
        
        <div v-if="loading" class="p-24 text-center">
          <i class="pi pi-spin pi-spinner text-4xl text-violet-400"></i>
          <p class="text-xl text-violet-200 mt-4">Sincronizando banco de dados...</p>
        </div>

        <div v-else class="p-6">
          <DataTable :value="products" responsiveLayout="scroll" :pt="{ root: { class: 'custom-table' } }">
            
            <Column header="Foto" style="width: 8%">
              <template #body="slotProps">
                <img 
                  v-if="slotProps.data.image" 
                  :src="formatImageUrl(slotProps.data.image)" 
                  class="w-16 h-16 object-cover rounded-xl border-2 border-[#3d1d85] shadow-lg"
                  @error="onImageError"
                />
                <div v-else class="w-16 h-16 bg-[#251052]/50 rounded-xl flex items-center justify-center border-2 border-dashed border-[#3d1d85]">
                  <i class="pi pi-image text-violet-400 text-xl"></i>
                </div>
              </template>
            </Column>

            <Column header="Categoria" style="width: 15%">
              <template #body="slotProps">
                <span v-if="slotProps.data.category" class="text-[10px] bg-violet-900/50 text-violet-300 px-3 py-1 rounded-full border border-violet-500 uppercase font-bold tracking-wider">
                  {{ slotProps.data.category.name }}
                </span>
                <span v-else class="text-xs text-violet-500 italic">Sem categoria</span>
              </template>
            </Column>

            <Column field="name" header="Produto" style="width: 30%">
              <template #body="slotProps">
                <div class="font-bold text-white text-xl">{{ slotProps.data.name }}</div>
                <div class="text-sm text-violet-300 italic line-clamp-1">{{ slotProps.data.description || 'Sem descrição' }}</div>
              </template>
            </Column>

            <Column field="price" header="Preço" style="width: 15%">
              <template #body="slotProps">
                <span class="text-emerald-400 font-bold text-lg">R$ {{ slotProps.data.price }}</span>
              </template>
            </Column>

            <Column header="Validade" style="width: 15%">
              <template #body="slotProps">
                <span :class="slotProps.data.expiration_date ? 'text-violet-100' : 'text-violet-500 italic text-sm'">
                  {{ slotProps.data.expiration_date ? formatDate(slotProps.data.expiration_date) : 'Indeterminada' }}
                </span>
              </template>
            </Column>

            <Column header="Ações" style="width: 10%; text-align: right">
              <template #body="slotProps">
                <div class="flex gap-3 justify-end">
                  <Button rounded class="!bg-emerald-500 !border-none w-9 h-9 flex items-center justify-center shadow-md" @click="openEditModal(slotProps.data)">
                    <i class="pi pi-pencil text-white text-sm"></i>
                  </Button>
                  <Button rounded class="!bg-red-600 !border-none w-9 h-9 flex items-center justify-center shadow-md" @click="deleteProduct(slotProps.data.id)">
                    <i class="pi pi-trash text-white text-sm"></i>
                  </Button>
                </div>
              </template>
            </Column>

            <template #empty>
              <div class="text-center p-10 text-violet-300">
                  <i class="pi pi-box text-5xl block mb-4 opacity-20"></i>
                  <p class="text-xl font-medium">Nenhum produto encontrado.</p>
              </div>
            </template>
          </DataTable>

          <div class="mt-8 flex justify-between items-center text-violet-400 px-2 border-t border-[#251052] pt-6">
            <span class="text-sm font-medium">Exibindo página {{ currentPage }} de {{ lastPage }}</span>
            <div class="flex gap-2">
              <Button icon="pi pi-chevron-left" @click="changePage(currentPage - 1)" :disabled="currentPage === 1" text class="!text-violet-400" label="Anterior" />
              <Button icon="pi pi-chevron-right" @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage" text class="!text-violet-400" label="Próxima" iconPos="right" />
            </div>
          </div>
        </div>
      </div>

      <Dialog v-model:visible="showModal" modal :header="editingId ? 'Editar Produto' : 'Cadastrar Novo Produto'" :style="{ width: '38rem' }" :pt="{ root: { class: 'custom-dialog' } }">
        <form @submit.prevent="submitProduct" class="flex flex-col gap-6 mt-4">
          
          <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-violet-400 uppercase tracking-widest">Nome do Produto</label>
            <input v-model="form.name" type="text" maxlength="50" required class="bg-[#11062b] border border-[#251052] rounded-xl p-3 text-white focus:border-violet-500 outline-none" placeholder="Ex: Maçã" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-2">
              <label class="text-xs font-bold text-violet-400 uppercase tracking-widest">Preço</label>
              <input v-model="form.price" type="number" step="0.01" min="0.01" required class="bg-[#11062b] border border-[#251052] rounded-xl p-3 text-white focus:border-violet-500" placeholder="3.99" />
            </div>

            <div class="flex flex-col gap-2">
              <label class="text-xs font-bold text-violet-400 uppercase tracking-widest">Categoria</label>
              <select v-model="form.category_id" required class="bg-[#11062b] border border-[#251052] rounded-xl p-3 text-white focus:border-violet-500 outline-none cursor-pointer">
                <option value="" disabled>Selecione...</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
          </div>

          <div class="bg-[#1a0b3b]/50 p-4 rounded-xl border border-[#251052]">
            <div class="flex items-center gap-3 mb-3">
              <input 
                type="checkbox" 
                v-model="hasExpiration" 
                :disabled="isFoodCategory" 
                id="hasExp" 
                class="w-5 h-5 accent-violet-500 cursor-pointer disabled:opacity-50" 
              />
              <label for="hasExp" class="text-sm font-bold text-white cursor-pointer">
                Este produto possui validade? <span v-if="isFoodCategory" class="text-violet-400 text-xs">(Obrigatório para Alimentos)</span>
              </label>
            </div>
            
            <div v-if="hasExpiration" class="flex flex-col gap-2 animate-fadein">
              <input v-model="form.expiration_date" type="date" :min="today" :required="hasExpiration" class="bg-[#11062b] border border-[#251052] rounded-lg p-3 text-white outline-none" />
            </div>
          </div>

          <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-violet-400 uppercase tracking-widest">Descrição (Opcional)</label>
            <textarea v-model="form.description" maxlength="200" rows="3" class="bg-[#11062b] border border-[#251052] rounded-xl p-3 text-white focus:border-violet-500" placeholder="Conte mais sobre o produto..."></textarea>
          </div>

          <div class="flex flex-col gap-3">
            <label class="text-xs font-bold text-violet-400 uppercase tracking-widest">Foto do Produto</label>
            <div v-if="imagePreview" class="flex justify-center mb-2">
              <img :src="imagePreview" class="w-40 h-40 object-cover rounded-2xl border-2 border-violet-500 shadow-2xl" />
            </div>
            <input @change="handleFileUpload" type="file" accept="image/*" :required="!editingId" class="bg-[#11062b] border border-[#251052] rounded-xl p-3 text-violet-300 file:bg-violet-900 file:text-white file:border-none file:rounded-md file:px-4 file:py-1 file:mr-4 file:font-bold" />
          </div>

          <div class="flex justify-end gap-3 mt-4 pt-6 border-t border-[#251052]">
            <Button label="Cancelar" text class="text-violet-400 font-bold" @click="showModal = false" />
            <Button :label="editingId ? 'Atualizar' : 'Salvar'" raised type="submit" :loading="isSaving" class="!bg-violet-600 !border-none font-bold px-8" />
          </div>
        </form>
      </Dialog>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import axios from 'axios';

const API_BASE_URL = 'http://localhost';
const router = useRouter();
const userName = ref('...');

const products = ref([]);
const categories = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const currentPage = ref(1);
const lastPage = ref(1);

const showModal = ref(false);
const isSaving = ref(false);
const editingId = ref(null);
const imagePreview = ref(null);
const hasExpiration = ref(false);

const form = ref({ name: '', description: '', price: '', expiration_date: '', category_id: '', image: null });

const today = computed(() => new Date().toISOString().split('T')[0]);

// LOGICA INTELIGENTE PARA CATEGORIA ALIMENTOS
const isFoodCategory = computed(() => {
  const selected = categories.value.find(c => c.id === form.value.category_id);
  return selected?.name === 'Alimentos';
});

watch(isFoodCategory, (val) => {
  if (val) hasExpiration.value = true;
});

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const [year, month, day] = dateStr.split('-');
  return `${day}/${month}/${year}`;
};

const fetchProducts = async (page = 1) => {
  try {
    loading.value = true;
    const token = localStorage.getItem('token');
    const response = await axios.get(`${API_BASE_URL}/api/v1/products`, {
      params: { search: searchQuery.value, page: page },
      headers: { 'Authorization': `Bearer ${token}` }
    });
    
    if (response.data.data) {
      products.value = response.data.data;
      currentPage.value = response.data.current_page;
      lastPage.value = response.data.last_page;
    } else {
      products.value = response.data;
    }
  } catch (error) { console.error(error); } finally { loading.value = false; }
};

const fetchCategories = async () => {
  try {
    const token = localStorage.getItem('token');
    const response = await axios.get(`${API_BASE_URL}/api/v1/categories`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    categories.value = response.data;
  } catch (error) { console.error(error); }
};

let searchTimer;
const handleSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => { fetchProducts(1); }, 500);
};

const changePage = (page) => {
  if (page >= 1 && page <= lastPage.value) { fetchProducts(page); }
};

const formatImageUrl = (path) => path.startsWith('http') ? path : `${API_BASE_URL}/storage/${path}`;
const onImageError = (e) => e.target.src = 'https://placehold.co/100x100/251052/a78bfa?text=Sem+Foto';

const openCreateModal = () => {
  editingId.value = null;
  imagePreview.value = null;
  hasExpiration.value = false;
  form.value = { name: '', description: '', price: '', expiration_date: '', category_id: '', image: null };
  showModal.value = true;
};

const openEditModal = (product) => {
  editingId.value = product.id;
  imagePreview.value = formatImageUrl(product.image);
  hasExpiration.value = !!product.expiration_date;
  form.value = { ...product, image: null };
  showModal.value = true;
};

watch(hasExpiration, (val) => {
  if (!val) form.value.expiration_date = '';
});

const handleFileUpload = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.value.image = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};

const submitProduct = async () => {
  try {
    isSaving.value = true;
    const token = localStorage.getItem('token');
    const fd = new FormData();
    Object.keys(form.value).forEach(key => {
      if (key === 'expiration_date' && !hasExpiration.value) return;
      if (form.value[key] !== null && form.value[key] !== undefined) {
        fd.append(key, form.value[key]);
      }
    });

    let url = `${API_BASE_URL}/api/v1/products`;
    if (editingId.value) {
      url += `/${editingId.value}`;
      fd.append('_method', 'PUT');
    }

    await axios.post(url, fd, {
      headers: { 'Content-Type': 'multipart/form-data', 'Authorization': `Bearer ${token}` }
    });
    showModal.value = false;
    fetchProducts(currentPage.value);
  } catch (error) { alert('Erro ao salvar.'); } finally { isSaving.value = false; }
};

const deleteProduct = async (id) => {
  if (!confirm('Deseja excluir?')) return;
  try {
    const token = localStorage.getItem('token');
    await axios.delete(`${API_BASE_URL}/api/v1/products/${id}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    fetchProducts(currentPage.value);
  } catch (error) { console.error(error); }
};

onMounted(() => {
  userName.value = localStorage.getItem('user_name') || 'Administrador';
  fetchCategories();
  fetchProducts();
});

const handleLogout = () => { localStorage.clear(); router.push('/login'); };
</script>

<style>
.custom-table .p-datatable-wrapper { background: transparent !important; }
.custom-table .p-datatable-thead > tr > th { 
  background: rgba(37, 16, 82, 0.4) !important; color: #a78bfa !important; border-bottom: 2px solid #251052 !important; padding: 1.2rem 1rem !important;
}
.custom-table .p-datatable-tbody > tr { background: transparent !important; color: white !important; }
.custom-table .p-datatable-tbody > tr:hover { background: rgba(37, 16, 82, 0.3) !important; }
.custom-table .p-datatable-tbody > tr > td { border-bottom: 1px solid #251052 !important; padding: 1.2rem 1rem !important; }

.custom-dialog { background: #1a0b3b !important; border: 1px solid #251052 !important; border-radius: 2rem !important; }
.p-dialog-header { background: transparent !important; color: white !important; border-bottom: 1px solid #251052 !important; padding: 1.5rem !important; }
.p-dialog-content { background: transparent !important; padding: 1.5rem !important; }

::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }

@keyframes fadein {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadein { animation: fadein 0.3s ease-out; }
</style>