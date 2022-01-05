<template>
    <Multiselect
    v-model="value"
    :placeholder="label"
    :options="options"
    :searchable="true"
    @change="(value) => { $emit('update', value) }"
    >
    <template v-slot:option="{ option }">
        {{ option.value }} [{{ option.label }}]
    </template>
    <template v-slot:singlelabel="{ value }">
        <div class="multiselect-single-label">
            {{ value.value }}
        </div>
    </template>
    </Multiselect>
</template>

<style src="@vueform/multiselect/themes/default.css"></style>

<script>
import Multiselect from '@vueform/multiselect'

export default {
    components: { Multiselect },
    props: ['value', 'options', 'label'],
    emits: ['update'],
}
</script>