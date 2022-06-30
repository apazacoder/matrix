<template>
    <div id="main">
        <div class="title-section">
            <h1>Usuarios</h1>
            <span class="controls">
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
            </span>
        </div>
        <v-card>
            <v-card-text>
                <users-table></users-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import UsersTable from './../components/users/UsersTable';

    require('./../axioswrapper');
    export default {
        name: "Users",
        components: {
            'users-table': UsersTable
        },
        data() {
            return {

            }
        },
        mounted(){
            // this.$store.dispatch('LOAD_USERS_LIST');

        }
    }
</script>

<style scoped>

</style>
