<template>
    <header v-if="!hideHeader">
        <div class="px-6 lg:px-16">
            <div class="max-w-screen-xl mx-auto">
                <div
                    class="flex flex-wrap items-center justify-between -mx-2 my-3 lg:my-6"
                >
                    <div class="mx-2 max-w-full">
                        <router-link class="block" to="/documents">
                            <img
                                class="h-[4rem]"
                                src="/images/logo.svg"
                                alt="Logo"
                            />
                        </router-link>
                    </div>

                    <div class="mx-2 w-80">
                        <input
                            :value="$route.query?.q"
                            placeholder="hledej"
                            class="appearance-none bg-transparent block border-2 border-black p-2 rounded-none w-full focus:shadow-input focus:shadow-neutral-300 focus:outline-none"
                            type="search"
                            @change="search"
                        />
                    </div>
                </div>
            </div>
        </div>

        <menu class="flex justify-center w-full">
            <li>
                <router-link
                    class="block border-b-2 border-transparent hover:border-black p-4"
                    :to="{ name: 'catalog', query: null }"
                    >Dokumenty</router-link
                >
            </li>
            <li>
                <router-link
                    class="block border-b-2 border-transparent hover:border-black p-4"
                    :to="{ name: 'info' }"
                    >Info</router-link
                >
            </li>
        </menu>
    </header>

    <main class="mb-12 lg:mb-24">
        <slot></slot>

        <div class="px-6 lg:px-16">
            <slot name="container"></slot>
        </div>
    </main>
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
            const q = e.target.value;
            this.$router.push({
                name: "catalog",
                query: { q },
            });
        },
    },
    computed: {
        hideHeader() {
            return "kiosk" in this.$route.query;
        },
    },
};
</script>
