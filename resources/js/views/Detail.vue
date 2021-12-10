<template>
    <div v-if="item">
        <h2>{{ item.document.content.title }}</h2>

        <p v-if="item.document.content.part_of_1">
            part_of_1:
            <router-link :to="{ name: 'catalog', query: { filter: { part_of_1: item.document.content.part_of_1 } } }">{{ item.document.content.part_of_1 }}</router-link>
        </p>
        <p v-if="item.document.content.part_of_2">
            part_of_2:
            <router-link :to="{ name: 'catalog', query: { filter: { part_of_2: item.document.content.part_of_2 } } }">{{ item.document.content.part_of_2 }}</router-link>
        </p>
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