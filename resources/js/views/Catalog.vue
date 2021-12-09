<template>
    <div v-for="(value, key) in query.filter" :key="key">
        <facet v-model="query.filter[key]" :options="options[key]" :label="key" @update:modelValue="update" />
    </div>

    <div v-for="(item, i) in items" :key="i">
        <router-link :to="{ name: 'detail', params: { id: item.document.id } }">{{ item.document.content.title }}</router-link>
    </div>
    <div ref="last"></div>
</template>

<script>
import Facet from '../components/Facet.vue'
import axios from 'axios'
import _ from 'lodash'

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
        this.query = _.merge(this.query, this.$route.query)
        this.$watch('query', function (query) {
            // force replace because query gets updated in current route
            // therefore isSameRouteLocation returns true and route is not replaced
            this.$router.replace({ query, force: true })
        }, {
            deep: true,
        })
        this.update()
    },
    methods: {
        fetchItems() {
            const params = this.filterParams(this.query.filter)
            params.set('page', this.page)
            axios.get(`/api/items`, { params })
                .then(({ data }) => {
                if (data.data.length) {
                    this.page += 1
                    this.items.push(...data.data)
                    this.$nextTick(() => {
                        this.observer.observe(this.$refs.last)
                    })
                }
            })
        },
        filterParams(filter) {
            // todo global stringify
            const queryString = this.$router.options.stringifyQuery({ filter })
            return new URLSearchParams(queryString)
        },
        fetchAggregations() {
            const params = this.filterParams(this.query.filter)
            // todo infinity
            params.set('size', 1000)
            const terms = Object.keys(this.query.filter)
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
        update() {
            this.page = 1
            this.items = []
            this.fetchAggregations()
            this.fetchItems()
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