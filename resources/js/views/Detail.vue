<template>
    <layout>
        <div v-if="item">
            <h2 class="mb-4">{{ item.title }}</h2>

            <div v-if="item.image_urls.length">
                <router-link :to="{ name: 'zoom', id: item.id }">
                    <img :src="item.image_urls[0]" :alt="item.title">
                </router-link>
            </div>

            <swiper
            @imagesReady="swiperTo(0)"
            @swiper="setSwiper"
            class="my-4"
            slidesPerView="auto">
                <swiper-slide class="!w-auto" v-for="(image_url, i) in item.image_urls" :key="i">
                    <img class="h-40" :src="image_url" />
                </swiper-slide>
            </swiper>

            <div class="flex justify-between my-4">
                <span class="cursor-pointer underline hover:no-underline" @click="swiperPrev">Prev</span>
                <span>{{ swiper?.realIndex + 1 }}/{{ swiper?.slides?.length }}</span>
                <span class="cursor-pointer underline hover:no-underline" @click="swiperNext">Next</span>
            </div>

            <div class="my-4">
                <p v-for="attribute in attributes.filter(key => item[key])" :key="attribute">
                    {{ labels[attribute] }}: {{ item[attribute] }}
                </p>

                <p v-if="item.part_of_1 || item.part_of_2">
                    {{ labels.part_of }}:
                    <router-link class="underline hover:no-underline" :to="{ name: 'catalog', query: { filter: { part_of: item.part_of_1 } } }">{{ item.part_of_1 }}</router-link><template v-if="item.part_of_1 && item.part_of_2">, </template>
                    <router-link class="underline hover:no-underline" :to="{ name: 'catalog', query: { filter: { part_of: item.part_of_2 } } }">{{ item.part_of_2 }}</router-link>
                </p>

                <p v-if="item.author || item.author_image">
                    {{ labels.authors }}:
                    <router-link class="underline hover:no-underline" :to="{ name: 'catalog', query: { filter: { authors: item.author } } }" v-if="item.author">{{ item.author }}</router-link><template v-if="item.author && item.author_image">,</template>
                    <router-link class="underline hover:no-underline" :to="{ name: 'catalog', query: { filter: { authors: item.author_image } } }" v-if="item.author_image">{{ item.author_imgae }}</router-link>
                </p>

                <p v-for="filterable in filterables.filter(key => item[key])" :key="filterable">
                    {{ labels[filterable] }}:
                    <router-link class="underline hover:no-underline" :to="{ name: 'catalog', query: { filter: { [filterable]: item[filterable] } } }">{{ item[filterable] }}</router-link>
                </p>

                <p v-if="item.archive_folder_references">
                    {{ labels.archive_folder_references }}:
                    <template v-for="(reference, i) in item.archive_folder_references" :key="i">
                        <template v-if="i">, </template>
                        <router-link :to="{ name: 'catalog', query: { filter: { archive_folder: reference } } }" class="underline hover:no-underline">
                            {{ reference }}
                        </router-link>
                    </template>
                </p>
            </div>

            <p class="my-4 whitespace-pre-wrap">
                {{ item.description }}
            </p>
        </div>
    </layout>
</template>

<script>
import Layout from '../components/Layout.vue'
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';

export default {
    components: { Layout, Swiper, SwiperSlide },
    data() {
        return {
            item: null,
            attributes: [
                'dating',
            ],
            filterables: [
                'institution',
                'archive_fund',
                'archive_box',
                'archive_folder',
                'work_type',
            ],
            swiper: null,
            labels: {
                part_of: 'Celek',
                authors: 'Autor',
                institution: 'Vlastník',
                archive_fund: 'Fond',
                archive_box: 'Karton',
                archive_folder: 'Složka',
                work_type: 'Dokument',
                dating: 'Datace',
                archive_folder_references: 'Související'
            },
        }
    },
    created() {
        this.fetch()
    },
    methods: {
        fetch() {
            axios.get(`/api/items/${this.$route.params.id}`, {
                params: { page: this.page }
            }).then(({ data }) => {
                this.item = data.data
            })
        },
        setSwiper(swiper) {
            this.swiper = swiper
        },
        swiperPrev() {
            this.swiper?.slidePrev()
        },
        swiperNext() {
            this.swiper?.slideNext()
        },
        swiperTo(index) {
            this.swiper?.slideTo(index)
        },
    }
}
</script>