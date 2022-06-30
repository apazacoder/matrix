<template>
    <div id="main">
        <div class="title-section"></div>
        <v-card>
            <v-card-title>
                <v-icon
                        large
                        left
                >
                    mdi-bug
                </v-icon>
                <span class="title font-weight-light">404</span>
            </v-card-title>

            <v-card-text class="headline font-weight-bold">
              Página no encontrada, <a href="/">Volver al inicio</a>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    export default {
        name: "NotFound"
    }
</script>

<style scoped>

</style>
