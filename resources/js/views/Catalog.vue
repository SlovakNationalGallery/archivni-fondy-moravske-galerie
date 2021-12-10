<template>
    <div v-for="(filterOptions, key) in options.filter" :key="key">
        <facet
        :label="key"
        :value="$route.query?.filter?.[key] ?? null"
        :options="filterOptions"
        @update="value => facetUpdate(value, key)" />
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
            options: {
                filter: {
                    part_of_1: [],
                    part_of_2: [],
                }
            },
            items: [],
            page: 1,
            observer: new IntersectionObserver(this.observerCallback),
        }
    },
    created() {
        this.update()
    },
    methods: {
        facetUpdate(value, key) {
            const query = _.merge(this.$route.query, { filter: { [key]: value } })
            this.$router.replace({ query })
            this.update()
        },
        fetchItems() {
            const params = this.filterParams()
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
        filterParams() {
            const queryString = this.$router.options.stringifyQuery(this.$route.query)
            return new URLSearchParams(queryString)
        },
        fetchAggregations() {
            const params = this.filterParams()
            // todo infinity
            params.set('size', 1000)
            Object.keys(this.options.filter).forEach(field => {
                params.append(`terms[${field}]`, field)
            })
            return axios
                .get(`/api/items/aggregations`, { params })
                .then(({ data }) => {
                    Object.entries(data).forEach(([facet, options]) => {
                        this.options.filter[facet] = options
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
    },
    watch: {
        '$route'() {
            this.update()
        }
    }
}
</script>