import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import { VueMasonryPlugin } from 'vue-masonry'
import App from './App.vue'
import Catalog from './views/Catalog.vue'
import Detail from './views/Detail.vue'
import Info from './views/Info.vue'
import Zoom from './views/Zoom.vue'
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
    }, {
        path: '/zoom/:id',
        component: Zoom,
        name: 'zoom',
    }, {
        path: '/info',
        component: Info,
        name: 'info',
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
    .use(VueMasonryPlugin)

app.mount('#app')