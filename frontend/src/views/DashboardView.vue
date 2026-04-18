<template>
  <div class="min-h-screen bg-[#11062b] font-sans text-slate-100">
    <Toast /> 
    <ConfirmDialog /> 
    
    <nav class="bg-[#1a0b3b]/50 backdrop-blur-md border-b border-[#251052] px-6 py-3 flex justify-between items-center shadow-lg sticky top-0 z-50">
      <div class="flex items-center gap-2">
        <div class="bg-violet-500/20 p-2 rounded-lg border border-violet-500/30">
          <i class="pi pi-box text-violet-400 text-2xl"></i>
        </div>
        <span class="text-xl font-bold tracking-tight text-white">Innyx Inventory</span>
      </div>

      <div class="flex items-center gap-6">
        <div class="flex items-center gap-3 bg-[#11062b]/50 p-1 pr-4 rounded-full border border-[#251052]">
          <div class="w-10 h-10 rounded-full bg-violet-600 flex items-center justify-center border-2 border-[#3d1d85] shadow-inner">
            <i class="pi pi-user text-white"></i>
          </div>
          <div class="text-left">
            <p class="text-[10px] text-violet-400 uppercase font-black tracking-tighter leading-none mb-1">Logado como</p>
            <p class="text-sm font-bold text-white leading-none">{{ userName }}</p>
          </div>
        </div>

        <button 
          @click="handleLogout" 
          class="group flex items-center gap-2 text-red-400 hover:text-red-300 transition-all font-bold text-sm"
        >
          <div class="p-2 rounded-lg bg-red-500/10 group-hover:bg-red-500/20 border border-red-500/20 transition-all">
            <i class="pi pi-power-off"></i>
          </div>
          <span class="hidden md:inline">Sair</span>
        </button>
      </div>
      <button 
  @click="deleteAccount" 
  class="flex items-center gap-2 text-violet-400 hover:text-red-400 transition-all font-bold text-sm border-r border-[#251052] pr-6 mr-2"
>
  <i class="pi pi-user-minus"></i>
  <span class="hidden md:inline">Excluir Conta</span>
</button>
    </nav>

    <main class="p-8 max-w-7xl mx-auto">
      <header class="flex justify-between items-end mb-10">
        <div>
          <h2 class="text-4xl font-black text-white mb-2">Produtos</h2>
          <p class="text-violet-300 text-lg">Gerenciamento completo de estoque.</p>
        </div>
        <Button label="Adicionar Novo" icon="pi pi-plus" raised class="!bg-emerald-500 !border-none px-6 py-3 font-bold" @click="openCreateModal" />
      </header>

      <div class="flex flex-col md:flex-row gap-4 mb-10">
        <div class="relative flex-1 shadow-2xl">
          <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-white z-10"></i>
          <input 
            v-model="searchQuery" 
            @input="handleSearch"
            type="text" 
            placeholder="Buscar por nome ou descrição..." 
            class="w-full bg-[#1a0b3b]/50 border border-[#251052] rounded-xl py-4 pl-12 pr-4 text-white focus:border-violet-500 outline-none transition-all text-lg"
          />
        </div>
        
        <Select 
          v-model="selectedCategory" 
          :options="categoryOptions" 
          optionLabel="name" 
          optionValue="id"
          placeholder="Todas as Categorias" 
          @change="fetchProducts(1)"
          class="w-full md:w-72 !bg-[#1a0b3b]/50 !border-[#251052] !rounded-xl !text-white"
          :pt="{
            label: { class: '!text-white !p-4' },
            dropdown: { class: '!text-violet-400' },
            overlay: { class: '!bg-[#1a0b3b] !border-[#251052] !shadow-2xl' },
            item: { class: '!text-white hover:!bg-violet-600/20 !p-3' }
          }"
        />
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
                  :src="slotProps.data.image" 
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
                <span v-if="slotProps.data.category" class="text-[10px] bg-violet-900/50 text-white px-3 py-1 rounded-full border border-violet-500 uppercase font-bold tracking-wider">
                  {{ slotProps.data.category.name }}
                </span>
                <span v-else class="text-xs text-white italic">Sem categoria</span>
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
                <span :class="slotProps.data.expiration_date ? 'text-violet-100' : 'text-white italic text-sm'">
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

          <div class="mt-8 flex justify-between items-center text-white px-2 border-t border-[#251052] pt-6">
            <span class="text-sm font-medium">Exibindo página {{ currentPage }} de {{ lastPage }}</span>
            <div class="flex gap-2">
              <Button icon="pi pi-chevron-left" @click="changePage(currentPage - 1)" :disabled="currentPage === 1" text class="!text-white" label="Anterior" />
              <Button icon="pi pi-chevron-right" @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage" text class="!text-white" label="Próxima" iconPos="right" />
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
              <Select 
                v-model="form.category_id" 
                :options="categories" 
                optionLabel="name" 
                optionValue="id"
                placeholder="Selecione..." 
                required
                class="!bg-[#11062b] !border-[#251052] !rounded-xl !text-white"
                :pt="{
                  label: { class: '!text-white !p-3' },
                  overlay: { class: '!bg-[#11062b] !border-[#251052]' },
                  item: { class: '!text-white hover:!bg-violet-600/20' }
                }"
              />
            </div>
          </div>

          <div class="bg-[#1a0b3b]/50 p-4 rounded-xl border border-[#251052]">
            <div class="flex items-center gap-3 mb-3">
              <input type="checkbox" v-model="hasExpiration" :disabled="isFoodCategory" id="hasExp" class="w-5 h-5 accent-violet-500 cursor-pointer disabled:opacity-50" />
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

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import Dialog from 'primevue/dialog';
import Select from 'primevue/select'; // <-- Importado
import api from '../services/api';

const router = useRouter();
const toast = useToast();
const confirmService = useConfirm(); 
const userName = ref('...');
const products = ref([]);
const categories = ref<any[]>([]);
const loading = ref(true);
const searchQuery = ref('');
const currentPage = ref(1);
const lastPage = ref(1);
const selectedCategory = ref(null);
const showModal = ref(false);
const isSaving = ref(false);
const editingId = ref<number | null>(null);
const imagePreview = ref<string | null>(null);
const hasExpiration = ref(false);

const form = ref({ 
  name: '', 
  description: '', 
  price: '', 
  expiration_date: '', 
  category_id: '' as string | number, 
  image: null as File | null 
});

// Opções de categorias injetando "Todas" no início
const categoryOptions = computed(() => {
  return [{ id: null, name: 'Todas as Categorias' }, ...categories.value];
});

const today = computed(() => new Date().toISOString().split('T')[0]);

const isFoodCategory = computed(() => {
  const selected = categories.value.find(c => c.id === form.value.category_id);
  return selected?.name === 'Alimentos';
});

watch(isFoodCategory, (val) => {
  if (val) hasExpiration.value = true;
});

const formatDate = (dateStr: string) => {
  if (!dateStr) return '';
  const [year, month, day] = dateStr.split('-');
  return `${day}/${month}/${year}`;
};

const fetchProducts = async (page = 1) => {
  try {
    loading.value = true;
    const response = await api.get('/products', {
      params: { search: searchQuery.value, page: page, category_id: selectedCategory.value }
    });
    products.value = response.data.data;
    currentPage.value = response.data.current_page;
    lastPage.value = response.data.last_page;
  } catch (error) { 
    console.error(error); 
  } finally { 
    loading.value = false; 
  }
};

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories');
    categories.value = response.data;
  } catch (error) { 
    console.error(error); 
  }
};

let searchTimer: any;
const handleSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => { fetchProducts(1); }, 500);
};

const changePage = (page: number) => {
  if (page >= 1 && page <= lastPage.value) { fetchProducts(page); }
};

const onImageError = (e: any) => e.target.src = 'https://placehold.co/100x100/251052/a78bfa?text=Sem+Foto';

const openCreateModal = () => {
  editingId.value = null;
  imagePreview.value = null;
  hasExpiration.value = false;
  form.value = { name: '', description: '', price: '', expiration_date: '', category_id: '', image: null };
  showModal.value = true;
};

const openEditModal = (product: any) => {
  editingId.value = product.id;
  imagePreview.value = product.image; 
  hasExpiration.value = !!product.expiration_date;
  form.value = { 
    name: product.name, 
    description: product.description, 
    price: product.price, 
    expiration_date: product.expiration_date, 
    category_id: product.category_id, 
    image: null 
  };
  showModal.value = true;
};

watch(hasExpiration, (val) => {
  if (!val) form.value.expiration_date = '';
});

const handleFileUpload = (e: any) => {
  const file = e.target.files[0];
  if (file) {
    form.value.image = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};

const submitProduct = async () => {
  try {
    isSaving.value = true;
    const fd = new FormData();
    for (const key in form.value) {
      const value = form.value[key as unknown as keyof typeof form.value];
      if (key === 'expiration_date' && !hasExpiration.value) continue;
      if (value !== null && value !== undefined) {
        if (value instanceof Blob) fd.append(key, value);
        else fd.append(key, String(value));
      }
    }
    let url = `/products`;
    if (editingId.value) {
      url += `/${editingId.value}`;
      fd.append('_method', 'PUT');
    }
    await api.post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
    showModal.value = false;
    fetchProducts(currentPage.value);
    toast.add({ severity: 'success', summary: 'Sucesso', detail: editingId.value ? 'Produto atualizado!' : 'Produto cadastrado!', life: 3000 });
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao salvar o produto.', life: 3000 });
  } finally {
    isSaving.value = false;
  }
};

const deleteProduct = (id: number) => {
  confirmService.require({
    message: 'Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.',
    header: 'Confirmação de Exclusão',
    icon: 'pi pi-exclamation-triangle',
    rejectLabel: 'Cancelar',
    acceptLabel: 'Excluir',
    rejectClass: 'p-button-secondary p-button-outlined',
    acceptClass: 'p-button-danger',
    accept: async () => {
      try {
        await api.delete(`/products/${id}`);
        fetchProducts(currentPage.value);
        toast.add({ severity: 'success', summary: 'Eliminado', detail: 'Produto removido com sucesso!', life: 3000 });
      } catch (error) { 
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Não foi possível eliminar o produto.', life: 3000 });
      }
    }
  });
};

onMounted(() => {
  userName.value = localStorage.getItem('user_name') || 'Administrador';
  fetchCategories();
  fetchProducts();
});

const handleLogout = () => { localStorage.clear(); router.push('/login'); };

const deleteAccount = () => {
  confirmService.require({
    message: 'ATENÇÃO: Isso apagará sua conta e TODOS os seus produtos permanentemente. Deseja continuar?',
    header: 'Excluir Minha Conta',
    icon: 'pi pi-exclamation-circle',
    acceptLabel: 'Sim, apagar tudo',
    rejectLabel: 'Cancelar',
    acceptClass: 'p-button-danger',
    accept: async () => {
      try {
        await api.delete('/user');
        localStorage.clear();
        router.push('/login');
        toast.add({ severity: 'success', summary: 'Conta Excluída', detail: 'Sentiremos sua falta!', life: 3000 });
      } catch (error) {
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Não foi possível excluir a conta.', life: 3000 });
      }
    }
  });
};
</script>

<style>
.custom-table .p-datatable-wrapper { background: transparent !important; }
.custom-table .p-datatable-thead > tr > th { 
  background: rgba(37, 16, 82, 0.4) !important; 
  color: white !important; 
  border-bottom: 2px solid #251052 !important; 
  padding: 1.2rem 1rem !important;
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