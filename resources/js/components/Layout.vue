<template>
    <div class="flex flex-col flex-wrap items-stretch min-h-full">
        <header class="overflow-hidden shrink-0">
            <div class="px-6 lg:px-16">
                <div class="max-w-screen-xl mx-auto">
                    <div class="flex flex-wrap items-center justify-between -mx-2 my-3 lg:my-6">
                        <div class="mx-2 max-w-full">
                            <router-link class="block" to="/documents">
                                <img class="h-[4rem]" src="/images/logo.svg" alt="Logo" >
                            </router-link>
                        </div>

                        <div class="mx-2 w-80">
                            <input :value="$route.query?.q" placeholder="hledej" class="block border-2 border-black p-2 w-full focus:shadow-input focus:shadow-neutral-300 focus:outline-none" type="search" @search="search">
                        </div>
                    </div>
                </div>
            </div>

            <menu class="flex justify-center w-full">
                <li><router-link class="block border-b-2 border-transparent hover:border-black p-4" :to="{ name: 'catalog', query: null }">Dokumenty</router-link></li>
                <li><router-link class="block border-b-2 border-transparent hover:border-black p-4" :to="{ name: 'info' }">Info</router-link></li>
            </menu>
        </header>

        <main class="grow shrink-0">
            <slot></slot>

            <div class="px-6 lg:px-16">
                <slot name="container"></slot>
            </div>
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
            this.$router.push({
                name: 'catalog',
                query: { q }
            })
        }
    }
}
</script>