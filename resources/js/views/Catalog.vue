<template>
    <div class="-mx-2">
        <div class="flex flex-wrap -my-2">
            <div class="my-2 px-2 w-full lg:w-1/2" v-for="(filterOptions, key) in options.filter" :key="key">
                <facet
                class="border-2 border-black"
                :label="key"
                :value="$route.query?.filter?.[key] ?? null"
                :options="filterOptions"
                @update="value => facetUpdate(value, key)" />
            </div>
        </div>
    </div>

    <slider
    class="mb-4 mt-12"
    :min="options.minYear"
    :max="options.maxYear"
    v-if="yearRange"
    v-model="yearRange"
    @update:modelValue="yearsUpdate" />

    <div class="-mx-2" item-selector="[data-masonry-tile]" transition-duration="0" v-masonry="masonry">
        <div class="flex flex-wrap mb-10">
            <div v-masonry-tile class="px-2 py-4 w-1/2 lg:w-1/4" v-for="(item, i) in items" :key="i" data-masonry-tile>
                <router-link :to="{ name: 'detail', params: { id: item.id } }">
                    <img @load="debouncedRedraw" class="w-full" :srcset="item.images?.[0]?.srcset" />
                    <div class="font-medium mt-1">{{ item.title }}</div>
                    <div class="italic mt-1">{{ item.dating }}</div>
                </router-link>
            </div>
        </div>
    </div>

</template>

<style src="@vueform/slider/themes/default.css"></style>

<script>
import Facet from '../components/Facet.vue'
import Slider from '@vueform/slider'
import axios from 'axios'
import _ from 'lodash'

export default {
    components: { Facet, Slider },
    data() {
        return {
            yearRange: null,
            masonry: 'masonry',
            options: {
                filter: {
                    part_of_1: [],
                    part_of_2: [],
                },
                minYear: 1900,
                maxYear: 2000,
            },
            items: [],
            page: 1,
            observer: new IntersectionObserver(this.observerCallback),
            debouncedRedraw: _.debounce(() => { this.$redrawVueMasonry(this.masonry) }, 100),
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
        yearsUpdate(value) {
            const query = _.merge(this.$route.query, {
                filter: {
                    date_earliest: { gte: value[0] },
                    date_latest: { lte: value[1] },
                }
            })
            this.$router.replace({ query, force: true })
        },
        fetchItems() {
            const params = this.filterParams()
            params.set('page', this.page)
            params.set('size', 12)
            axios.get(`/api/items`, { params })
                .then(({ data }) => {
                if (data.data.length) {
                    this.page += 1
                    this.items.push(...data.data)
                    this.$nextTick(() => {
                        this.$redrawVueMasonry(this.masonry)
                        const last = document.querySelector('[data-masonry-tile]:last-child')
                        this.observer.observe(last)
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
        fetchYears() {
            const params = this.filterParams()
            params.delete('filter[date_earliest][gte]')
            params.delete('filter[date_latest][lte]')
            params.append(`min[date_earliest]`, 'date_earliest')
            params.append(`max[date_latest]`, 'date_latest')
            return axios
                .get(`/api/items/aggregations`, { params })
                .then(({ data }) => {
                    this.options.minYear = data.date_earliest
                    this.options.maxYear = data.date_latest
                    this.yearRange = [
                        this.yearRange?.[0] ?? this.options.minYear,
                        this.yearRange?.[1] ?? this.options.maxYear,
                    ]
                })
        },
        update() {
            this.page = 1
            this.items = []
            this.fetchAggregations()
            this.fetchItems()
            this.fetchYears()
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
        '$route'(value) {
            this.yearRange = [
                value.query?.filter?.date_earliest?.gte,
                value.query?.filter?.date_latest?.lte,
            ]
            this.update()
        }
    }
}
</script>