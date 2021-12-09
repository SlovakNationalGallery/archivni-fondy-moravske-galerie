<template>
    <select v-model="modelValue" @update:modelValue="$emit('update:modelValue', modelValue)">
        <option :value="null">{{ label }}</option>
        <option v-for="(count, key) in options" :key="key" :value="key">{{ key }} [{{ count }}]</option>
    </select>
</template>

<script>
export default {
    props: ['modelValue', 'options', 'label'],
    emits: ['update:modelValue'],
}
</script>