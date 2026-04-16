import axios from 'axios';

const api = axios.create({
    // Endereço do Laravel
    baseURL: 'http://localhost/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

export default api;