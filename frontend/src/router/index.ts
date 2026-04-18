import { createRouter, createWebHistory } from 'vue-router';

const Login = () => import('../views/LoginView.vue');
const Dashboard = () => import('../views/DashboardView.vue');

const routes = [
    {
        path: '/',
        redirect: '/login'
    },
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
    path: '/register',
    name: 'Register',
    component: () => import('../views/RegisterView.vue')
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true } 
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Verifica antes de mudar de ecrã
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');

    if (to.meta.requiresAuth && !token) {
        next({ name: 'Login' });
    } else if (to.name === 'Login' && token) {
        next({ name: 'Dashboard' });
    } else {
        next();
    }
});

export default router;