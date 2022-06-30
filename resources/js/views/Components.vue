<template>
    <div id="main">
        <div class="title-section">
            <h1>Componentes</h1>
        </div>

            <v-card>
                <v-card-title>
                    Botones
                </v-card-title>
                <v-card-text>
                    <v-btn class="btn-green">
                        <v-icon left>mdi-plus</v-icon>
                        <span>Crear usuario</span>
                    </v-btn>

                    <v-btn class="btn-red">
                        <v-icon left>mdi-delete</v-icon>
                        <span>Borrar usuario</span>
                    </v-btn>

                    <v-btn class="btn-yellow">
                        <v-icon left>mdi-lead-pencil</v-icon>
                        <span>Editar usuario</span>
                    </v-btn>
                    <v-btn class="btn-blue">
                        <v-icon left>mdi-printer</v-icon>
                        <span>Imprimir</span>
                    </v-btn>
                    <v-btn class="btn-excel">
                        <v-icon left>mdi-file-excel</v-icon>
                        <span>Exportar excel</span>
                    </v-btn>
                </v-card-text>
            </v-card>

    </div>
</template>

<script>
    export default {
        name: "Components"
    }
</script>

<style scoped>

</style>
