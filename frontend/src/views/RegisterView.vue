<template>
  <div class="min-h-screen bg-[#11062b] flex items-center justify-center font-sans p-6">
    <Toast />
    <div class="w-full max-w-md bg-[#1a0b3b]/50 backdrop-blur-xl border border-[#251052] rounded-3xl p-10 shadow-2xl shadow-black/50">
      
      <div class="text-center mb-10">
        <div class="bg-violet-600 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-violet-600/20">
          <i class="pi pi-user-plus text-white text-3xl"></i>
        </div>
        <h1 class="text-3xl font-black text-white tracking-tight">Criar Conta</h1>
        <p class="text-violet-300 mt-2 font-medium">Junte-se ao Innyx Inventory</p>
      </div>

      <form @submit.prevent="handleRegister" class="flex flex-col gap-5">
        <div class="flex flex-col gap-2">
          <label class="text-xs font-bold text-violet-400 uppercase tracking-widest ml-1">Nome Completo</label>
          <div class="relative">
            <i class="pi pi-user absolute left-4 top-1/2 -translate-y-1/2 text-violet-400"></i>
            <input v-model="form.name" type="text" required class="w-full bg-[#11062b] border border-[#251052] rounded-xl py-3 pl-12 pr-4 text-white focus:border-violet-500 outline-none transition-all" placeholder="Seu nome" />
          </div>
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-xs font-bold text-violet-400 uppercase tracking-widest ml-1">E-mail</label>
          <div class="relative">
            <i class="pi pi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-violet-400"></i>
            <input v-model="form.email" type="email" required class="w-full bg-[#11062b] border border-[#251052] rounded-xl py-3 pl-12 pr-4 text-white focus:border-violet-500 outline-none transition-all" placeholder="exemplo@innyx.com" />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-violet-400 uppercase tracking-widest ml-1">Senha</label>
            <input v-model="form.password" type="password" required class="w-full bg-[#11062b] border border-[#251052] rounded-xl py-3 px-4 text-white focus:border-violet-500 outline-none transition-all" placeholder="••••••••" />
          </div>
          <div class="flex flex-col gap-2">
            <label class="text-xs font-bold text-violet-400 uppercase tracking-widest ml-1">Confirmar</label>
            <input v-model="form.password_confirmation" type="password" required class="w-full bg-[#11062b] border border-[#251052] rounded-xl py-3 px-4 text-white focus:border-violet-500 outline-none transition-all" placeholder="••••••••" />
          </div>
        </div>

        <Button label="Cadastrar e Entrar" type="submit" :loading="loading" raised class="!bg-violet-600 !border-none !rounded-xl !py-4 font-bold text-lg mt-4 shadow-lg shadow-violet-600/20" />
      </form>

      <div class="text-center mt-8">
        <p class="text-violet-300">
          Já tem uma conta? 
          <router-link to="/login" class="text-violet-400 font-bold hover:text-white transition-colors">Fazer Login</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import Toast from 'primevue/toast';

const router = useRouter();
const toast = useToast();
const loading = ref(false);

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

const handleRegister = async () => {
  try {
    loading.value = true;
    
    // 1. Faz a requisição de registro
    await api.post('/register', form.value);
    
    // 2. Avisa que deu certo
    toast.add({ 
      severity: 'success', 
      summary: 'Conta Criada!', 
      detail: 'Agora você já pode entrar com seu e-mail e senha.', 
      life: 4000 
    });
    
    // 3. Redireciona para o Login após um pequeno delay para o usuário ler o Toast
    setTimeout(() => {
      router.push('/login');
    }, 2000);

  } catch (error: any) {
    // Se o erro for 422 (validação), o Laravel envia as mensagens específicas
    const detail = error.response?.data?.message || 'Erro ao criar conta. Verifique os dados.';
    toast.add({ 
      severity: 'error', 
      summary: 'Falha no Registro', 
      detail, 
      life: 5000 
    });
  } finally {
    loading.value = false;
  }
};
</script>