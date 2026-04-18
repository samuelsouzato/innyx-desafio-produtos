<template>
  <div class="min-h-screen bg-[#11062b] flex items-center justify-center font-sans p-6">
    <Toast />
    <div class="w-full max-w-md bg-[#1a0b3b]/50 backdrop-blur-xl border border-[#251052] rounded-3xl p-10 shadow-2xl shadow-black/50">
      
      <div class="text-center mb-10">
        <div class="bg-violet-600 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-violet-600/20">
          <i class="pi pi-box text-white text-3xl"></i>
        </div>
        <h1 class="text-3xl font-black text-white tracking-tight">Bem-vindo</h1>
        <p class="text-violet-300 mt-2 font-medium">Gerencie seu estoque com precisão</p>
      </div>

      <form @submit.prevent="handleLogin" class="flex flex-col gap-6">
        <div class="flex flex-col gap-2">
          <label class="text-xs font-bold text-violet-400 uppercase tracking-widest ml-1">E-mail</label>
          <div class="relative">
            <i class="pi pi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-violet-400"></i>
            <input 
              v-model="email" 
              type="email" 
              required 
              class="w-full bg-[#11062b] border border-[#251052] rounded-xl py-3 pl-12 pr-4 text-white focus:border-violet-500 outline-none transition-all" 
              placeholder="admin@innyx.com" 
            />
          </div>
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-xs font-bold text-violet-400 uppercase tracking-widest ml-1">Senha</label>
          <div class="relative">
            <i class="pi pi-lock absolute left-4 top-1/2 -translate-y-1/2 text-violet-400"></i>
            <input 
              v-model="password" 
              type="password" 
              required 
              class="w-full bg-[#11062b] border border-[#251052] rounded-xl py-3 pl-12 pr-4 text-white focus:border-violet-500 outline-none transition-all" 
              placeholder="••••••••" 
            />
          </div>
        </div>

        <Button 
          label="Entrar no Sistema" 
          type="submit" 
          :loading="loading" 
          raised 
          class="!bg-violet-600 !border-none !rounded-xl !py-4 font-bold text-lg mt-2 shadow-lg shadow-violet-600/20" 
        />
      </form>

      <div class="text-center mt-10">
        <p class="text-violet-300">
          Não tem uma conta? 
          <router-link to="/register" class="text-violet-400 font-bold hover:text-white transition-colors">Cadastre-se</router-link>
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
const email = ref('');
const password = ref('');
const loading = ref(false);

const handleLogin = async () => {
  try {
    loading.value = true;
    const response = await api.post('/login', {
      email: email.value,
      password: password.value
    });

    localStorage.setItem('token', response.data.access_token);
    localStorage.setItem('user_name', response.data.user.name);
    
    router.push('/dashboard');
  } catch (error: any) {
    toast.add({ 
      severity: 'error', 
      summary: 'Erro de Acesso', 
      detail: 'E-mail ou senha incorretos.', 
      life: 3000 
    });
  } finally {
    loading.value = false;
  }
};
</script>