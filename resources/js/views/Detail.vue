<template>
    <div v-if="item">
        <h2 class="mb-4">{{ item.title }}</h2>

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

        <p class="mt-4 whitespace-pre-wrap">
            {{ item.description }}
        </p>
    </div>
</template>

<script>
export default {
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
                this.item = data.document.content
            })
        }
    }
}
</script>