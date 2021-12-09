import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import Catalog from './views/Catalog.vue'
import Detail from './views/Detail.vue'
import qs from 'qs'


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
    }],
    parseQuery: qs.parse,
    stringifyQuery(query) {
        return qs.stringify(query, {
            filter: (prefix, value) => {
                if (value === null) {
                    return
                }
                return value
            },
            encodeValuesOnly: true
        })
    },
})

const app = createApp(App)
    .use(router)

app.mount('#app')