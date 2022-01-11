<template>
    <Multiselect
    v-model="value"
    track-by="value"
    no-options-text="žádné záznamy"
    :options="formattedOptions"
    :placeholder="label"
    :searchable="true"
    @input="(value) => { $emit('update', value) }"
    :classes="{
        container: 'relative mx-auto w-full flex items-center justify-end box-border cursor-pointer border border-gray-300 rounded bg-white text-base leading-snug outline-none',
        containerDisabled: 'cursor-default bg-gray-100',
        containerOpen: 'rounded-b-none',
        containerOpenTop: 'rounded-t-none',
        containerActive: '',
        search: 'w-full absolute inset-0 outline-none focus:ring-0 appearance-none box-border border-0 text-base font-sans bg-white rounded pl-3.5',
        placeholder: 'flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5 text-gray-400',
        caret: 'bg-multiselect-caret bg-center bg-no-repeat w-2.5 h-4 py-px box-content mr-3.5 relative z-10 opacity-40 flex-shrink-0 flex-grow-0 transition-transform transform pointer-events-none',
        caretOpen: 'rotate-180 pointer-events-auto',
        clear: 'pr-3.5 relative z-10 opacity-40 transition duration-300 flex-shrink-0 flex-grow-0 flex hover:opacity-80',
        clearIcon: 'bg-multiselect-remove bg-center bg-no-repeat w-2.5 h-4 py-px box-content inline-block',
        spinner: 'bg-multiselect-spinner bg-center bg-no-repeat w-4 h-4 z-10 mr-3.5 animate-spin flex-shrink-0 flex-grow-0',
        dropdown: 'max-h-60 absolute -left-px -right-px bottom-0 transform translate-y-full border border-gray-300 -mt-px overflow-y-scroll z-50 bg-white flex flex-col rounded-b',
        dropdownTop: '-translate-y-full top-px bottom-auto flex-col-reverse rounded-b-none rounded-t',
        dropdownHidden: 'hidden',
        options: 'flex flex-col p-0 m-0 list-none',
        optionsTop: 'flex-col-reverse',
        option: 'flex items-center justify-start box-border text-left cursor-pointer text-base leading-snug py-2 px-3',
        optionPointed: 'text-gray-800 bg-gray-100',
        optionSelected: 'text-white bg-gray-500',
        optionDisabled: 'text-gray-300 cursor-not-allowed',
        optionSelectedPointed: 'text-white bg-gray-500 opacity-90',
        optionSelectedDisabled: 'text-gray-100 bg-gray-500 bg-opacity-50 cursor-not-allowed',
        noOptions: 'py-2 px-3 text-gray-600 bg-white text-left',
        noResults: 'py-2 px-3 text-gray-600 bg-white text-left',
        fakeInput: 'bg-transparent absolute left-0 right-0 -bottom-px w-full h-px border-0 p-0 appearance-none outline-none text-transparent',
        spacer: 'h-9 py-px box-content',
    }"
    >
    <template v-slot:singlelabel="{ value }">
        <div class="flex items-center h-full max-w-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5 pr-16 box-border">
            {{ value.value }}
        </div>
    </template>
    </Multiselect>
</template>

<script>
import Multiselect from '@vueform/multiselect'

export default {
    components: { Multiselect },
    props: ['value', 'options', 'label'],
    emits: ['update'],
    computed: {
        formattedOptions() {
            return this.options.map(option => {
                option.label = `${option.value} [${option.count}]`
                return option
            })
        }
    }
}
</script>