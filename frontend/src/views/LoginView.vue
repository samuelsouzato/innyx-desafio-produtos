<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Message from 'primevue/message';

const router = useRouter();
const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');

const handleLogin = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    
    const response = await api.post('/login', { 
  email: email.value,
  password: password.value
});

    localStorage.setItem('token', response.data.access_token);
    
    router.push('/dashboard');
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erro ao tentar fazer login. Verifique suas credenciais!';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="flex items-center justify-center min-h-screen bg-slate-900 p-4">
    <div class="w-full max-w-md bg-slate-800 p-8 rounded-2xl shadow-2xl border border-slate-700">
      
      <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-white mb-2">Bem-vindo</h2>
        <p class="text-slate-400">Entre com sua conta para gerenciar produtos</p>
      </div>

      <form @submit.prevent="handleLogin" class="flex flex-col gap-4">
        
        <div class="flex flex-col gap-2">
          <label for="email" class="text-slate-300 font-medium">E-mail</label>
          <InputText id="email" v-model="email" placeholder="seu@email.com" class="w-full" />
        </div>

        <div class="flex flex-col gap-2">
          <label for="password" class="text-slate-300 font-medium">Senha</label>
          <Password id="password" v-model="password" :feedback="false" toggleMask class="w-full" inputClass="w-full" />
        </div>

        <Message v-if="error" severity="error" variant="simple" size="small">{{ error }}</Message>

        <Button 
          type="submit" 
          label="Entrar" 
          icon="pi pi-sign-in" 
          :loading="loading" 
          class="mt-4 w-full p-button-lg" 
        />

      </form>

    </div>
  </div>
</template>