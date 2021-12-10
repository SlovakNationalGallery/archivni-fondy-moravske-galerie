<template>
    <div class="flex flex-wrap -mx-2 w-full">
        <div class="px-2 w-full lg:w-1/2" v-for="(filterOptions, key) in options.filter" :key="key">
            <facet
            class="border-2 border-black"
            :label="key"
            :value="$route.query?.filter?.[key] ?? null"
            :options="filterOptions"
            @update="value => facetUpdate(value, key)" />
        </div>
    </div>

    <div class="flex flex-wrap -mx-2 w-full" item-selector="[data-masonry-tile]" transition-duration="0" v-masonry="masonry">
        <div v-masonry-tile class="px-2 py-4 w-1/4" v-for="(item, i) in items" :key="i" data-masonry-tile>
            <router-link :to="{ name: 'detail', params: { id: item.id } }">
                <img @load="debouncedRedraw" class="w-full" :src="`https://via.placeholder.com/500x${randomHeight()}`" />
                <div class="font-medium mt-1">{{ item.title }}</div>
                <div class="italic mt-1">{{ item.dating }}</div>
            </router-link>
        </div>
        <div ref="last"></div>
    </div>
</template>

<script>
import Facet from '../components/Facet.vue'
import axios from 'axios'
import _ from 'lodash'

export default {
    components: { Facet },
    data() {
        return {
            masonry: 'masonry',
            options: {
                filter: {
                    part_of_1: [],
                    part_of_2: [],
                }
            },
            items: [],
            page: 1,
            observer: new IntersectionObserver(this.observerCallback),
            debouncedRedraw: _.debounce(() => { this.$redrawVueMasonry(this.masonry) }, 1),
        }
    },
    created() {
        this.update()
    },
    methods: {
        randomHeight() {
            const items = ['200', '300', '400', '500', '600', '700'];
            return items[Math.floor(Math.random() * items.length)]
        },
        facetUpdate(value, key) {
            const query = _.merge(this.$route.query, { filter: { [key]: value } })
            this.$router.replace({ query, force: true })
        },
        fetchItems() {
            const params = this.filterParams()
            params.set('page', this.page)
            axios.get(`/api/items`, { params })
                .then(({ data }) => {
                if (data.data.length) {
                    this.page += 1
                    this.items.push(...data.data.map(item => item.document.content))
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