<template>
    <div class="flex flex-col flex-wrap items-stretch min-h-full max-w-screen-xl mx-auto w-full">
        <header class="shrink-0">
            <h1 class="font-medium py-4 text-xl"><router-link :to="{ name: 'catalog', query: null }">Archivní fondy Moravské galerie</router-link></h1>
        </header>
        <main class="grow shrink-0">
            <router-view />
        </main>
        <footer class="py-4 shrink-0 text-right"><a href="https://lab.sng.sk/">lab.SNG</a></footer>
    </div>
</template>