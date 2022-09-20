<template>
    <button type="button" @click="click">
        <slot>Zpět</slot>
    </button>
</template>

<script>
export default {
    props: ["fallback"],
    methods: {
        click() {
            if (
                document.referrer &&
                new URL(document.referrer).host !== window.location.host
            ) {
                this.$router.push(this.fallback);
            } else {
                this.$router.back();
            }
        },
    },
};
</script>
