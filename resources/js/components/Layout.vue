<template>
    <div class="flex flex-col flex-wrap items-stretch min-h-full max-w-screen-xl mx-auto px-8 w-full">
        <header class="my-4 shrink-0">
            <h1 class="font-medium text-xl"><router-link :to="{ name: 'catalog', query: null }">Archivní fondy Moravské galerie</router-link></h1>

            <menu class="flex justify-center w-full">
                <li><router-link class="block border-b-2 border-transparent hover:border-black p-4" :to="{ name: 'catalog', query: null }">Dokumenty</router-link></li>
                <li><router-link class="block border-b-2 border-transparent hover:border-black p-4" :to="{ name: 'info' }">Info</router-link></li>
            </menu>
        </header>
        <main class="grow shrink-0">
            <slot></slot>
        </main>
        <footer class="py-4 shrink-0 text-right"><a href="https://lab.sng.sk/">lab.SNG</a></footer>
    </div>
</template>

<style scoped>
.router-link-active {
    border-color: black;
}
</style>