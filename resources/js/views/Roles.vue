<template>
    <div id="main">
        <div class="title-section"></div>
        <v-card>
            <v-data-table
                    class="elevation-1 centered-th centered-td three-actions table-roles"
                    loading-text="Cargando... Espere por favor"
                    dense
                    :headers="headers"
                    :items="roles"
                    :loading="isProcessing"
                    :footer-props="tableFooterProps"
                    :options="tableOptions"
                    :search="search"
            >
                <template v-slot:item.text_permissions="{ item }">
                    <span v-for="permission in item.permissions">
                        <v-chip small
                                color="blue-grey lighten-1"
                                text-color="white"
                        >
                            <span v-text="permission.text"></span>
                        </v-chip>
                   </span>
                </template>
                <template v-slot:top>
                    <v-toolbar flat color="white">
                        <v-toolbar-title>Roles</v-toolbar-title>
                        <v-divider
                                class="mx-4"
                                inset
                                vertical
                        ></v-divider>
                        <v-spacer></v-spacer>
                        <v-text-field
                                v-model="search"
                                append-icon="mdi-magnify"
                                label="Buscar"
                                single-line
                                hide-details
                        ></v-text-field>
                        <v-spacer></v-spacer>
                        <v-dialog v-model="dialog" content-class="v-dialog-dense v-dialog-extra-large">
                            <template v-slot:activator="{ on }">
                                <v-btn class="btn-green" v-on="on" @click="showForAction('create')">
                                    <v-icon left>mdi-plus</v-icon>
                                    <span>Crear rol</span>
                                </v-btn>
                            </template>
                            <v-card>
                                <v-card-title>
                                    <span class="headline">{{ formTitle }}</span>
                                </v-card-title>

                                <v-card-text>
                                    <v-container class="without-padding dense">
                                        <v-row>
                                            <v-col cols="12" sm="6" md="6">
                                                <v-text-field v-model="editedItem.name"
                                                              dense @keyup.enter="save"
                                                              :disabled="editionIsDisabled"
                                                              label="Nombre"></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="6">
                                                <v-text-field v-model="editedItem.text"
                                                              dense @keyup.enter="save"
                                                              :disabled="editionIsDisabled"
                                                              label="Texto"></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="12" md="12">Permisos del rol</v-col>

                                            <v-col cols="12" sm="4" md="3"
                                                   v-for="(permission, i) in permissions" :key="i">
                                                <v-checkbox v-model="editedItem.permissions"
                                                            :label="permission.text"
                                                            :disabled="editionIsDisabled"
                                                            :value="permission"
                                                            dense
                                                ></v-checkbox>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                    <div class="progress-wrapper">
                                        <v-progress-linear
                                                :active="isProcessing"
                                                :indeterminate="true"
                                                :rounded="false"
                                                color="teal darken-2"
                                        ></v-progress-linear>
                                    </div>
                                </v-card-text>
                                <v-container v-if="errors.length > 0" class="without-vertical-padding">
                                    <v-alert
                                            text
                                            prominent
                                            type="error"
                                            icon="mdi-alert-circle"
                                    >
                                        <div v-for="error in errors" v-text="error"></div>
                                    </v-alert>
                                </v-container>
                                <v-card-actions>
                                    <v-alert v-if="currentAction == 'delete'"
                                             icon="mdi-alert"
                                             dense text type="warning">
                                        ¿Confirma que quiere borrar?
                                    </v-alert>
                                    <v-spacer></v-spacer>
                                    <v-btn :color="currentAction == 'delete' ? 'red lighten-1' : 'teal darken-1'"
                                           text
                                           @click="save"
                                           :disabled="isProcessing"
                                           v-if="currentAction != 'read'">
                                        <v-icon left v-if="currentAction != 'delete'">mdi-content-save</v-icon>
                                        <v-icon left v-if="currentAction == 'delete'">mdi-trash-can</v-icon>
                                        <span v-text="currentAction == 'delete' ? 'Borrar': 'Guardar'"></span>
                                    </v-btn>
                                    <v-btn color="blue darken-1" text @click="close"
                                           :disabled="isProcessing">
                                        <v-icon left>mdi-close</v-icon>
                                        <span v-text="currentAction == 'read' ? 'Cerrar': 'Cancelar'"></span>
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </v-toolbar>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-icon
                            :color="readColor"
                            v-if="$can('read', 'roles')"
                            class="mr-2"
                            :disabled="isProcessing"
                            @click="showForAction('read', item)">
                        mdi-eye
                    </v-icon>
                    <v-icon
                            :color="updateColor"
                            v-if="$can('update', 'roles')"
                            class="mr-2"
                            :disabled="isProcessing"
                            @click="showForAction('update', item)">
                        mdi-pencil
                    </v-icon>
                    <v-icon
                            :color="deleteColor"
                            v-if="$can('delete', 'roles')"
                            :disabled="isProcessing"
                            @click="showForAction('delete', item)">
                        mdi-delete
                    </v-icon>
                </template>
                <template v-slot:no-data>
                    No existen registros
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>

<script>
    import {dataTableOptions as dtOptions} from './../vuetify-custom';

    require('./../axioswrapper');
    export default {
        data: () => ({
            dialog: false,
            headers: [
                {text: 'Nombre', value: 'name'},
                {text: 'Texto', value: 'text'},
                {text: 'Permisos', value: 'text_permissions', sortable: false},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            // item
            roles: [],
            // related
            permissions: [],

            editedIndex: -1, // -1: para crear usuario, cualquier otro para editar usuario
            editedItem: {
                name: '',
                text: '',
                permissions: [],
            },
            defaultItem: {
                name: '',
                text: '',
                permissions: [],
            },

            errors: [],
            currentAction: '',

            isProcessing: true,

            // table options
            tableFooterProps: dtOptions.tableFooterProps,
            tableOptions: dtOptions.tableOptions,

            createColor: dtOptions.createColor,
            readColor: dtOptions.readColor,
            updateColor: dtOptions.updateColor,
            deleteColor: dtOptions.deleteColor,

            // search
            search: ''
        }),

        computed: {
            formTitle() {
                switch (this.currentAction) {
                    case 'create':
                        return 'Crear rol';
                        break;
                    case 'read':
                        return 'Ver rol';
                        break;
                    case 'update':
                        return 'Editar rol';
                        break;
                    case 'delete':
                        return 'Borrar rol';
                        break;
                }
            },
            editionIsDisabled() {
                // deshabilitamos edición si está cargando, viendo o borrando
                return this.isProcessing || this.currentAction == 'read' || this.currentAction == 'delete';
            }
        },
        created() {
            this.initialize();
        },

        methods: {
            initialize() {
                this.getRoles();
            },
            getRoles(callback = null) {
                this.isProcessing = true;
                axiosGetApi('/roles', res => {
                    this.roles = res.data.roles;
                    this.permissions = res.data.permissions;
                    if (callback) {
                        callback();
                    }
                    this.isProcessing = false;
                });
            },
            updateRole() {
                this.isProcessing = true;
                let editedIndex = this.editedIndex;
                axiosPutApi(`/roles/${this.editedItem.id}`, this.editedItem, res => {
                    if (res.data.hasOwnProperty("errors") && res.data.errors.length > 0) {
                        this.errors = res.data.errors;
                        this.isProcessing = false;
                    } else {
                        this.getRoles(
                            () => {
                                setTimeout(() => {
                                    showSuccessMessage(res.data.okMessage);
                                    this.animateRow(editedIndex);
                                }, 200);
                                this.close();
                            }
                        );
                    }
                });
            },

            animateRow(editedIndex) {
                let rowIndex = editedIndex % $("tbody tr", ".table-roles").length + 1;
                $("tbody tr:nth-child(" + rowIndex + ")", ".table-roles").addClass('updated-row');
            },

            storeRole(allOk = null) {
                this.isProcessing = true;

                axiosPostApi('/roles', this.editedItem, res => {
                    if (res.data.hasOwnProperty("errors") && res.data.errors.length > 0) {
                        this.errors = res.data.errors;
                        this.isProcessing = false;
                    } else {
                        this.getRoles(
                            () => {
                                setTimeout(() => {
                                    showSuccessMessage(res.data.okMessage);
                                }, 200);
                                this.close();
                            }
                        )
                    }
                });
            },

            destroyRole() {
                this.isProcessing = true;

                axiosDeleteApi(`/roles/${this.editedItem.id}`, {}, res => {
                    if (res.data.hasOwnProperty("errors") && res.data.errors.length > 0) {
                        this.errors = res.data.errors;
                        this.isProcessing = false;
                    } else {
                        this.getRoles(
                            () => {
                                setTimeout(() => {
                                    showSuccessMessage(res.data.okMessage);
                                }, 200);
                                this.close();
                            }
                        )
                    }
                });
            },

            showForAction(action, item = null) {
                this.currentAction = action;
                if (!item) { // no item, load the default
                    item = this.defaultItem;
                }
                this.loadItem(item);
            },
            loadItem(item) {
                this.editedIndex = this.roles.indexOf(item)
                this.editedItem = Object.assign({}, item)
                this.dialog = true
            },

            close() {
                this.errors = [];
                this.dialog = false

                this.$nextTick(() => {
                    this.editedItem = Object.assign({}, this.defaultItem);
                    this.editedIndex = -1;
                });
            },

            save() {
                switch (this.currentAction) {
                    case 'create':
                        this.storeRole();
                        break;
                    case 'read':
                        this.close();
                        break;
                    case 'update':
                        this.updateRole();
                        break;
                    case 'delete':
                        this.destroyRole();
                        break;
                }
            },
        },
    }
</script>

<style scoped>

</style>
