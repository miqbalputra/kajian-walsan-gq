import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// XSRF-TOKEN is HttpOnly; use the same CSRF meta token as fetch/Livewire.
window.axios.interceptors.request.use((config) => {
    const base = new URL(config.baseURL || '', window.location.href);
    const target = new URL(config.url || '', base);
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (target.origin === window.location.origin && token) {
        config.headers.set('X-CSRF-TOKEN', token);
    }
    return config;
});
