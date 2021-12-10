<template>
    <select v-model="value" @update:modelValue="$emit('update', value)">
        <option :value="null">{{ label }}</option>
        <option v-for="(count, key) in options" :key="key" :value="key">{{ key }} [{{ count }}]</option>
    </select>
</template>

<script>
export default {
    props: ['value', 'options', 'label'],
    emits: ['update'],
}
</script>