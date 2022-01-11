<template>
    <layout>
        <div class="bg-gray-200 px-6 lg:px-16 py-8">
            <div class="-mx-2">
                <div class="flex flex-wrap -my-2">
                    <div class="my-2 px-2 w-full lg:w-1/3" v-for="(filterOptions, key) in options.filter" :key="key">
                        <facet
                        :label="labels[key]"
                        :value="$route.query?.filter?.[key] ?? null"
                        :options="filterOptions"
                        @update="value => facetUpdate(value, key)" />
                    </div>
                </div>
            </div>

            <slider
            class="mb-2 mt-14 px-6 lg:px-0"
            :min="options.minYear"
            :max="options.maxYear"
            v-if="yearRange[0] && yearRange[1]"
            v-model="yearRange"
            @update:modelValue="yearsUpdate" />
        </div>

        <template v-slot:container>
            <div class="my-6 lg:my-8 text-lg" v-if="total">{{ total }} {{ totalLabel }}</div>
            <div class="my-6 lg:my-8 text-center text-lg" v-else-if="!loading">Nebyly nalezeny žádné záznamy</div>
            <div class="my-6 lg:my-8" v-else><img class="mx-auto" src="/images/loader.svg"></div>

            <div class="-mx-2 -my-4" item-selector="[data-masonry-tile]" transition-duration="0" v-masonry="masonry">
                <div class="flex flex-wrap mb-10">
                    <div v-masonry-tile class="px-2 py-4 w-1/2 lg:w-1/4 xl:w-1/5" v-for="(item, i) in items" :key="i" data-masonry-tile>
                        <router-link :to="{ name: 'detail', params: { id: item.id } }">
                            <img @load="debouncedRedraw" class="w-full" :src="imagePreviewUrl(item.images[0], 400)" />
                            <div class="mt-2">{{ item.title }}</div>
                            <div class="mt-1">{{ item.dating }}</div>
                        </router-link>
                    </div>
                </div>
            </div>
        </template>

    </layout>
</template>

<style src="@vueform/slider/themes/default.css"></style>

<script>
import Facet from '../components/Facet.vue'
import Layout from '../components/Layout.vue'
import Slider from '@vueform/slider'
import axios from 'axios'
import _ from 'lodash'
import { apiMixin } from '../mixins'

export default {
    mixins: [ apiMixin ],
    components: { Facet, Layout, Slider },
    data() {
        return {
            loading: true,
            total: 0,
            yearRange: [],
            masonry: 'masonry',
            options: {
                filter: {
                    part_of: [],
                    authors: [],
                    institution: [],
                    archive_fund: [],
                    work_type: [],
                },
                minYear: null,
                maxYear: null,
            },
            labels: {
                part_of: 'Celek',
                authors: 'Autor',
                institution: 'Vlastník',
                archive_fund: 'Fond',
                work_type: 'Dokument',
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
        search(e) {
            const query = _.merge(this.$route.query, { q: e.target.value })
            this.$router.replace({ query, force: true })
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
            params.set('size', 20)
            return axios
                .get(`/api/items`, { params })
                .then(({ data }) => {
                    this.total = data.total
                    this.loading = false
                    if (data.data.length) {
                        this.page += 1
                        this.items.push(...data.data)
                        this.$nextTick(() => {
                            this.$redrawVueMasonry(this.masonry)
                            const last = document.querySelector('[data-masonry-tile]:last-child')
                            if (last) {
                                this.observer.observe(last)
                            }
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
            this.loading = true
            this.page = 1
            this.items = []
            this.observer.disconnect()
            this.fetchItems()
            this.fetchAggregations()
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
    computed: {
        totalLabel() {
            if (this.total === 1) {
                return 'dokument'
            } else if (this.total >= 2 && this.total <= 4) {
                return 'dokumenty'
            } else {
                return 'dokumentů'
            }
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