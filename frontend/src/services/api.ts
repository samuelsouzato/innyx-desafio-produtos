import axios from 'axios';
import router from '../router';

const api = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Intercetor de Pedido: Injeta o Token em TODAS as chamadas automaticamente
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Intercetor de Resposta: erros 401 (Não autorizado)
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('user_name');
            router.push('/login');
        }
        return Promise.reject(error);
    }
);

export default api;