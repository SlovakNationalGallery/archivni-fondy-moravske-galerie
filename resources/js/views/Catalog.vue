<template>
    <div v-for="(value, key) in query.filter" :key="key">
        <facet v-model="query.filter[key]" :options="options[key]" :label="key" />
    </div>

    <div v-for="(item, i) in items" :key="i">
        <router-link :to="{ name: 'detail', params: { id: item.document.id } }">{{ item.document.content.title }}</router-link>
    </div>
    <div ref="last"></div>
</template>

<script>
import Facet from '../components/Facet.vue'
import axios from 'axios'

export default {
    components: { Facet },
    data() {
        return {
            query: {
                filter: {
                    part_of_1: null,
                    part_of_2: null,
                },
            },
            options: {
                part_of_1: [],
                part_of_2: [],
            },
            items: [],
            page: 1,
            observer: new IntersectionObserver(this.observerCallback),
        }
    },
    created() {
        this.fetchItems()
        this.fetchAggregations()
    },
    methods: {
        fetchItems() {
            axios.get(`/api/items`, {
                params: { page: this.page }
            }).then(({ data }) => {
                if (data.data.length) {
                    this.page += 1
                    this.items.push(...data.data)
                    this.$nextTick(() => {
                        this.observer.observe(this.$refs.last)
                    })
                }
            })
        },
        fetchAggregations() {
            const params = new URLSearchParams({
                size: 1000
            })
            const terms = ['part_of_1', 'part_of_2']
            terms.forEach(field => {
                params.append(`terms[${field}]`, field)
            })
            return axios
                .get(`/api/items/aggregations`, { params })
                .then(({ data }) => {
                    Object.entries(data).forEach(([facet, options]) => {
                        this.options[facet] = options
                    })
                })
        },
        observerCallback(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.observer.unobserve(entry.target)
                    this.fetchItems()
                }
            })
        }
    }
}
</script>