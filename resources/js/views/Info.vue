<template>
    <layout>
        <template v-slot:container>
            <div class="max-w-screen-xl mx-auto">
                <div class="max-w-2xl">
                    <h2 class="max-w-xl mt-12 text-2xl lg:text-4xl">
                        Digitální archiv Obrazárny Moravského zemského muzea
                    </h2>

                    <div class="mb-12 lg:mb-24 lg:text-xl">
                        <p class="mt-4 lg:mt-6">
                            Specializovaná veřejná databáze vznikla v rámci
                            Programu na podporu aplikovaného výzkumu a vývoje
                            národní a kulturní identity na léta 2016 až 2022
                            (NAKI II) – projekt Moravská zemská obrazárna –
                            tradice, reprezentace, identifikace (identifikační
                            kód projektu: DG20P02OVV026; příjemce účelové
                            podpory: Moravská galerie v Brně).
                        </p>
                        <p class="mt-4 lg:mt-6">
                            Specializovaná veřejná databáze archivních a
                            fotografických fondů Obrazárny Moravského zemského
                            muzea zpřístupňuje širokému okruhu zájemců
                            archiválie týkající se akvizic a dalších činností
                            historické Obrazárny Moravského zemského muzea, jež
                            je od roku 1961 součástí Moravské galerie v Brně.
                            Zpřístupněné dokumenty jsou dnes fyzicky rozdělené
                            mezi Moravskou galerii v Brně a Moravské zemské
                            muzeum a dosud byly jen obtížně přístupné. Součástí
                            databáze jsou i digitalizované inventární knihy,
                            plakáty a unikátní negativy, fotografie a
                            diapozitivy z původního fotoarchivu Obrazárny. Díky
                            dokumentaci a digitalizaci provedené v rámci
                            projektu se zájemce může seznámit s archivními
                            zprávami a obrazovými materiály spojenými s
                            konkrétními uměleckými díly nejen ze zkoumaného
                            historického sbírkového fondu, ale i dalšími
                            dochovanými na jiných místech nebo dokonce in situ.
                        </p>

                        <p class="mt-8 lg:mt-12">
                            Vyrobil a spravuje
                            <!--a
                                class="underline hover:no-underline"
                                href="https://lab.sng.sk/"
                            -->lab.SNG<!--/a
                            -->
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </layout>
</template>

<script>
import Layout from "../components/Layout.vue";

export default {
    components: { Layout },
};
</script>
