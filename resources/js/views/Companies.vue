<template>
    <div id="main">
        <div class="title-section"></div>
        <v-card>
            <v-stepper v-model="e6" vertical>
                <v-stepper-step :complete="e6 > 1" step="1">Datos de la empresa</v-stepper-step>
                <v-stepper-content step="1">
                    <v-row>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="Razón social"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="NIT"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="Teléfonos"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="Ciudad"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="12" md="12">
                            <v-text-field dense
                                          label="Dirección"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="12" md="12">
                            <v-text-field dense
                                          label="Rubro"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12">
                            <p>Modalidad de facturación:</p>
                        </v-col>
                        <v-col cols="12">
                            <v-radio-group v-model="row" row>
                                <v-radio label="Normal" value="radio-1"></v-radio>
                                <v-radio label="Con tasa cero" value="radio-2"></v-radio>
                                <v-radio label="Turística" value="radio-3"></v-radio>
                                <v-radio label="Exportaciones" value="radio-4"></v-radio>
                                <v-radio label="Con recibo de alquiler" value="radio-5"></v-radio>
                            </v-radio-group>
                        </v-col>
                    </v-row>
                    <v-btn color="primary" @click="e6 = 2">Continuar</v-btn>
                </v-stepper-content>

                <v-stepper-step :complete="e6 > 2" step="2">Dosificaciones</v-stepper-step>
                <v-stepper-content step="2">
                    <v-row>
                        <v-col>
                            <p>Dosificación normal</p>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="No. de autorización"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="Fecha"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="No. de Ley"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="Llave"></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col>
                            <p>Dosificación tasa cero</p>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="No. de autorización"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="Fecha"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="No. de Ley"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="Llave"></v-text-field>
                        </v-col>
                    </v-row>

                    <v-btn color="secondary" @click="e6 = 1">Atrás</v-btn>
                    <v-btn color="primary" @click="e6 = 3">Continuar</v-btn>

                </v-stepper-content>

                <v-stepper-step step="3">Parámetros de facturación</v-stepper-step>
                <v-stepper-content step="3">
                    <v-row>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="Tamaño de papel" value="Medio oficio"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="Tipo de facturación por defecto" value="Servicios"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                            <v-text-field dense
                                          label="Autoalimentación de clientes" value="Habilitada"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-btn color="secondary" @click="e6 = 2">Atrás</v-btn>
                    <v-btn color="primary" text>Finalizar</v-btn>
                </v-stepper-content>
            </v-stepper>
        </v-card>
    </div>
</template>

<script>
    require('./../axioswrapper');

    export default {
        data() {
            return {
                e6: 1,
                row: ""
            }
        },
    }
</script>

<style scoped>

</style>
