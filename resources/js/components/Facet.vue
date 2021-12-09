<template>
    <select v-model="modelValue">
        <option>{{ label }}</option>
        <option v-for="(count, key) in options" :key="key" :value="key">{{ key }} [{{ count }}]</option>
    </select>
</template>

<script>
export default {
    props: ['modelValue', 'options', 'label'],
}
</script>