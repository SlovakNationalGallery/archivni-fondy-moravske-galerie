import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import Catalog from './views/Catalog.vue'


require('./bootstrap');

const router = createRouter({
    history: createWebHistory(),
    routes: [{
        path: '/',
        component: Catalog,
    }]
})

const app = createApp(App)
    .use(router)

app.mount('#app')