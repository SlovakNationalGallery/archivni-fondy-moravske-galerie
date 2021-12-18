<template>
    <div v-if="item">
        <h2 class="mb-4">{{ item.title }}</h2>

        <div v-if="item.image_urls.length">
            <img :src="item.image_urls[0]" :alt="item.title">
        </div>

        <div class="my-4">
            <p v-for="filterable in filterables" :key="filterable">
                {{ filterable }}:
                <router-link class="underline hover:no-underline" :to="{ name: 'catalog', query: { filter: { [filterable]: item[filterable] } } }">{{ item[filterable] }}</router-link>
            </p>

            <p v-for="attribute in attributes" :key="attribute">
                {{ attribute }}: {{ item[attribute] }}
            </p>

            <p>
                archive_folder_references:
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
    </div>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';

export default {
    components: { Swiper, SwiperSlide },
    data() {
        return {
            item: null,
            filterables: [
                'part_of_1',
                'part_of_2',
            ],
            attributes: [
                'dating',
                'institution',
                'archive_fund',
                'archive_box',
                'archive_folder',
                'archive_file',
                'work_type',
            ],
            swiper: null,
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