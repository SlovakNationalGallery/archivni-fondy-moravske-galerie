<template>
    <div id="zoom">
        <div
            id="viewer"
            class="absolute h-full left-0 m-0 min-h-full p-0 top-0 w-full"
        ></div>
        <div id="toolbar" class="absolute m-5 right-0 text-right top-0">
            <button aria-label="Zoom in" id="zoom-in">
                <i class="fa fa-plus"></i>
            </button>
            <button aria-label="Zoom out" id="zoom-out">
                <i class="fa fa-minus"></i>
            </button>
            <button
                v-show="item?.images.length > 1"
                aria-label="Previous"
                id="previous"
            >
                <i class="fa fa-arrow-up"></i>
            </button>
            <button
                v-show="item?.images.length > 1"
                aria-label="Next"
                id="next"
            >
                <i class="fa fa-arrow-down"></i>
            </button>
        </div>
    </div>

    <div class="bottom-0 fixed px-6 py-2 w-full">
        <BackButton
            class="border-black border-1 bg-white px-4 py-3"
            :fallback="{ name: 'detail', params: { id: $route.params.id } }"
        />
    </div>
</template>

<script>
import BackButton from "../components/BackButton.vue";
import { apiMixin } from "../mixins";

import OpenSeadragon from "openseadragon";

export default {
    components: { BackButton },
    mixins: [apiMixin],
    data() {
        return {
            item: null,
        };
    },
    mounted() {
        this.fetch(this.$route.params.id).then(({ data }) => {
            this.item = data;
            this.$nextTick(() => {
                this.initViewer();
            });
        });
    },
    methods: {
        fetch() {
            return axios.get(`/api/items/${this.$route.params.id}`, {
                params: { page: this.page },
            });
        },
        initViewer() {
            const tileSources = this.item.images.map((image) =>
                this.imageZoomUrl(image)
            );

            const options = {
                id: "viewer",
                toolbar: "toolbar",
                zoomInButton: "zoom-in",
                zoomOutButton: "zoom-out",
                tileSources,
            };

            if (this.item.images.length > 1) {
                Object.assign(options, {
                    autoHideControls: false,
                    nextButton: "next",
                    previousButton: "previous",
                    sequenceMode: true,
                    showReferenceStrip: true,
                    referenceStripSizeRatio: 0.07,
                    referenceStripScroll: "vertical",
                    initialPage: this.$route.query.page,
                });
            }

            this.viewer = OpenSeadragon(options);
            this.$nextTick(() => {
                this.viewer.referenceStrip.currentSelected.setAttribute(
                    "data-current-page",
                    ""
                );
            });

            this.viewer.addHandler("page", function ({ eventSource }) {
                eventSource.referenceStrip.element
                    .querySelector("[data-current-page]")
                    ?.removeAttribute("data-current-page");
                eventSource.referenceStrip.currentSelected.setAttribute(
                    "data-current-page",
                    ""
                );
            });
        },
    },
};
</script>
