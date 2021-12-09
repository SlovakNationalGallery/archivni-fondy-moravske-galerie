<template>
    <div v-if="item">
        {{ item.document.content.title }}
    </div>
</template>

<script>
export default {
    data() {
        return {
            item: null,
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
                this.item = data
            })
        }
    }
}
</script>