export const apiMixin = {
    methods: {
        imagePreviewUrl(image, size) {
            return `${process.env.MIX_IMAGES_HOST}/preview/?path=${image}&size=${size}`
        },
        imageZoomUrl(image) {
            return `${process.env.MIX_IMAGES_HOST}/zoom/?path=${image}.dzi`
        },
    }
}