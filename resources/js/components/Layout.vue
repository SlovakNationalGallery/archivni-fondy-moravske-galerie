<template>
    <div class="flex flex-col flex-wrap items-stretch min-h-full">
        <header class="shrink-0">
            <div class="max-w-screen-xl mx-auto px-16">
                <div class="flex flex-wrap items-center justify-between my-6">
                    <h1 class="font-medium text-3xl whitespace-nowrap"><router-link :to="{ name: 'catalog', query: null }"><span class="font-bold uppercase">Moravská galerie</span> / Archivní fondy</router-link></h1>

                    <div class="my-2 w-80">
                        <input placeholder="hledej" class="block border-2 border-black p-2 w-full focus:shadow-input focus:shadow-neutral-300 focus:outline-none" type="text" @keyup.enter="search">
                    </div>
                </div>

                <menu class="flex justify-center w-full">
                    <li><router-link class="block border-b-2 border-transparent hover:border-black p-4" :to="{ name: 'catalog', query: null }">Dokumenty</router-link></li>
                    <li><router-link class="block border-b-2 border-transparent hover:border-black p-4" :to="{ name: 'info' }">Info</router-link></li>
                </menu>
            </div>
        </header>

        <main class="grow shrink-0">
            <slot></slot>
        </main>

        <footer class="my-6 shrink-0 text-center">
            Vyrobil a spravuje <a class="underline hover:no-underline" href="https://lab.sng.sk/">lab.SNG</a>
        </footer>
    </div>
</template>

<style scoped>
.router-link-active {
    border-color: black;
}
</style>

<script>
export default {
    methods: {
        search(e) {
            const q = e.target.value
            this.$route.push()
        }
    }
}
</script>