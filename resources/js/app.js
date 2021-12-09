import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import Catalog from './views/Catalog.vue'
import Detail from './views/Detail.vue'


require('./bootstrap');

const router = createRouter({
    history: createWebHistory(),
    routes: [{
        path: '/',
        component: Catalog,
        name: 'catalog',
    }, {
        path: '/detail/:id',
        component: Detail,
        name: 'detail',
    }]
})

const app = createApp(App)
    .use(router)

app.mount('#app')