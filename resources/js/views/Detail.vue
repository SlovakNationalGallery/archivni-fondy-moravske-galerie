<template>
    <layout>
        <template v-slot:container>
            <div class="isolate max-w-screen-xl mx-auto" v-if="item">
                <h2
                    class="max-w-screen-md mt-6 mx-auto text-2xl lg:text-4xl text-center"
                >
                    {{ item.title }}
                </h2>

                <p
                    class="mt-1 lg:mt-2 text-center lg:text-lg"
                    v-if="item.part_of_1 || item.part_of_2"
                >
                    <router-link
                        class="underline hover:no-underline"
                        :to="{
                            name: 'catalog',
                            query: { filter: { part_of: item.part_of_1 } },
                        }"
                        >{{ item.part_of_1 }}</router-link
                    ><template v-if="item.part_of_1 && item.part_of_2"
                        >,
                    </template>
                    <router-link
                        class="underline hover:no-underline"
                        :to="{
                            name: 'catalog',
                            query: { filter: { part_of: item.part_of_2 } },
                        }"
                        >{{ item.part_of_2 }}</router-link
                    >
                </p>

                <div class="flex flex-wrap -mx-4 mt-6 lg:mt-12">
                    <div class="mb-4 px-4 lg:w-1/2">
                        <div v-if="item.images.length">
                            <router-link
                                class="block relative"
                                :to="{
                                    name: 'zoom',
                                    id: item.id,
                                    query: { page: currentImage },
                                }"
                            >
                                <img
                                    :src="
                                        imagePreviewUrl(
                                            item.images[currentImage],
                                            800
                                        )
                                    "
                                    :alt="item.title"
                                    class="mx-auto"
                                />
                                <span
                                    class="absolute bg-black/60 h-10 leading-10 rounded-full right-2 text-center text-white w-10 top-2"
                                    ><i class="fa fa-search-plus"
                                /></span>
                            </router-link>
                        </div>

                        <template v-if="item.images.length > 1">
                            <div class="relative">
                                <swiper
                                    @imagesReady="swiperTo(0)"
                                    @swiper="setSwiper"
                                    class="my-4"
                                    slidesPerView="auto"
                                >
                                    <swiper-slide
                                        class="pr-4 last:pr-0 !w-auto"
                                        v-for="(image, i) in item.images"
                                        :key="i"
                                    >
                                        <img
                                            class="cursor-pointer h-40"
                                            :src="imagePreviewUrl(image, 400)"
                                            @click="currentImage = i"
                                        />
                                    </swiper-slide>
                                </swiper>

                                <div
                                    class="absolute bg-white/80 cursor-pointer h-10 leading-10 left-2 rounded-full text-center top-1/2 -translate-y-1/2 w-10 z-10"
                                    @click="swiperPrev"
                                >
                                    <i class="fa fa-arrow-left"></i>
                                </div>
                                <div
                                    class="absolute bg-white/80 cursor-pointer h-10 leading-10 rounded-full right-2 text-center top-1/2 -translate-y-1/2 w-10 z-10"
                                    @click="swiperNext"
                                >
                                    <i class="fa fa-arrow-right"></i>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="px-4 lg:text-lg lg:w-1/2">
                        <p
                            class="mb-1.5"
                            v-for="attribute in attributes.filter(
                                (key) => item[key]
                            )"
                            :key="attribute"
                        >
                            {{ labels[attribute] }}: {{ item[attribute] }}
                        </p>

                        <p
                            class="mb-1.5"
                            v-if="item.author || item.author_image"
                        >
                            {{ labels.authors }}:
                            <router-link
                                class="underline hover:no-underline"
                                :to="{
                                    name: 'catalog',
                                    query: { filter: { authors: item.author } },
                                }"
                                v-if="item.author"
                                >{{ item.author }}</router-link
                            ><template v-if="item.author && item.author_image"
                                >,
                            </template>
                            <router-link
                                class="underline hover:no-underline"
                                :to="{
                                    name: 'catalog',
                                    query: {
                                        filter: { authors: item.author_image },
                                    },
                                }"
                                v-if="item.author_image"
                                >{{ item.author_image }}</router-link
                            >
                        </p>

                        <p
                            class="mb-1.5"
                            v-for="filterable in filterables.filter(
                                (key) => item[key]
                            )"
                            :key="filterable"
                        >
                            {{ labels[filterable] }}:
                            <router-link
                                class="underline hover:no-underline"
                                :to="{
                                    name: 'catalog',
                                    query: {
                                        filter: {
                                            [filterable]: item[filterable],
                                        },
                                    },
                                }"
                                >{{ item[filterable] }}</router-link
                            >
                        </p>

                        <p class="mb-1.5" v-if="item.archive_folder_references">
                            {{ labels.archive_folder_references }}:
                            <template
                                v-for="(
                                    reference, i
                                ) in item.archive_folder_references"
                                :key="i"
                            >
                                <template v-if="i">, </template>
                                <router-link
                                    :to="{
                                        name: 'catalog',
                                        query: {
                                            filter: {
                                                archive_folder: reference,
                                            },
                                        },
                                    }"
                                    class="underline hover:no-underline"
                                >
                                    {{ reference }}
                                </router-link>
                            </template>
                        </p>

                        <p class="mb-1.5" v-if="item.entities">
                            {{ labels.entities }}:
                            <template
                                v-for="(entity, i) in item.entities"
                                :key="i"
                            >
                                <template v-if="i">, </template>
                                <router-link
                                    :to="{
                                        name: 'catalog',
                                        query: {
                                            filter: {
                                                entities: entity,
                                            },
                                        },
                                    }"
                                    class="underline hover:no-underline"
                                >
                                    {{ entity }}
                                </router-link>
                            </template>
                        </p>

                        <p class="my-4 whitespace-pre-wrap">
                            {{ item.description }}
                        </p>
                    </div>
                </div>
            </div>
            <div v-else>
                <img class="mx-auto" src="/images/loader.svg" />
            </div>
        </template>
    </layout>

    <div class="bg-gray-200 bottom-0 fixed px-6 py-2 w-full">
        <BackButton
            class="border-black border-1 bg-white px-4 py-3"
            :fallback="{ name: 'catalog' }"
        />
    </div>
</template>

<script>
import BackButton from "../components/BackButton.vue";
import Layout from "../components/Layout.vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import { apiMixin } from "../mixins";

export default {
    mixins: [apiMixin],
    components: { BackButton, Layout, Swiper, SwiperSlide },
    data() {
        return {
            currentImage: 0,
            item: null,
            attributes: ["dating"],
            filterables: [
                "institution",
                "archive_fund",
                "archive_box",
                "archive_folder",
                "work_type",
            ],
            swiper: null,
            labels: {
                authors: "Autor",
                institution: "Vlastník",
                archive_fund: "Fond",
                archive_box: "Karton",
                archive_folder: "Složka",
                work_type: "Dokument",
                dating: "Datace",
                archive_folder_references: "Související",
                entities: "Entity (místa, jména)",
            },
        };
    },
    created() {
        this.fetch();
    },
    methods: {
        fetch() {
            return axios
                .get(`/api/items/${this.$route.params.id}`, {
                    params: { page: this.page },
                })
                .then(({ data }) => {
                    this.item = data;
                    console.log(data);
                })
                .catch(() => {
                    this.$router.push({
                        name: "catalog",
                    });
                });
        },
        setSwiper(swiper) {
            this.swiper = swiper;
        },
        swiperPrev() {
            this.swiper?.slidePrev();
        },
        swiperNext() {
            this.swiper?.slideNext();
        },
        swiperTo(index) {
            this.swiper?.slideTo(index);
        },
    },
};
</script>
