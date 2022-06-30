<template>
    <div id="top-nav">
        <v-btn id="menu-btn"
               x-large
               text
               color="blue-grey darken-2"
        >
            <v-icon left>mdi-menu</v-icon>
            <span class="d-none d-sm-flex">
            Menú
                </span>
        </v-btn>
        <span class="nav-title">MATRIZ DE SEGUIMIENTO</span>
        <ul id="right-menu">
            <v-menu
                    v-if="!notificationsAreDisabled"
                    v-model="notificationsValue"
                    offset-y
                    transition="slide-y-transition"
                    :close-on-content-click="false"
            >
                <template v-slot:activator="{ on }">
                    <v-btn class="btn-notifications"
                           x-large
                           :color="notificationsQty ? 'blue-grey darken-2' : 'blue-grey lighten-4'"
                           v-on="on"
                           text
                    >
                        <v-badge :color="notificationsQty ? 'yellow darken-4' : 'blue-grey lighten-4'"
                                 overlap>
                            <template v-slot:badge>
                                <span v-text="notificationsQty"></span>
                            </template>
                            <v-icon>mdi-bell</v-icon>
                        </v-badge>
                    </v-btn>
                </template>
                <v-card max-width="360">
                    <v-card-title class="blue darken-4 white--text flex-center">
                        <span v-text="notificationsQty"></span>
                    </v-card-title>
                    <v-card-subtitle
                            class="blue darken-4 white--text center"
                    >
                        <span v-if="notificationsQty == 1"> nueva notificación</span>
                        <span v-else-if="notificationsQty > 1">nuevas notificaciones</span>
                        <span v-else>notificaciones</span>
                    </v-card-subtitle>
                    <v-card-text v-if="notificationsMenuItems.length > 0">
                        <div v-for="(item, index) in notificationsMenuItems"
                             :key="index">
                            <v-list-item :ripple="false"
                                         three-line
                                         @click="handleTopMenu(item.action)">
                                <v-list-item-content>
                                    <v-list-item-title>Primary content</v-list-item-title>
                                    <v-list-item-subtitle>{{ item.content }}</v-list-item-subtitle>
                                    <v-list-item-subtitle>continuación content</v-list-item-subtitle>
                                </v-list-item-content>
                                <v-list-item-action>
                                    <v-btn icon>
                                        <v-icon color="grey lighten-1">mdi-close</v-icon>
                                    </v-btn>
                                </v-list-item-action>
                            </v-list-item>
                            <v-divider v-if="index !== notificationsMenuItems.length - 1"></v-divider>
                        </div>
                    </v-card-text>
                </v-card>
            </v-menu>

            <v-menu
                    v-model="accountValue"
                    close-on-click
                    offset-y
                    transition="slide-y-transition"
            >
                <template v-slot:activator="{ on }">
                    <v-btn class="btn-account"
                           x-large
                           color="blue-grey darken-2 white--text"
                           v-on="on"
                           text
                    ><span class="d-none d-sm-flex" v-text="username">

                        </span>
                        <v-icon right>mdi-account-circle</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item
                            v-for="(item, index) in accountMenuItems"
                            :key="index"
                            @click="handleTopMenu(item.action)"
                    >
                        <v-list-item-title>
                            <v-icon>{{ item.icon }}</v-icon>
                            {{ item.title }}
                        </v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </ul>
        <v-dialog
                content-class=" v-dialog-dense v-dialog-small"
                v-model="showPassDialog"
        >
            <v-card>
                <v-card-title class="headline">
                    Cambiar contraseña
                </v-card-title>
                <v-card-text>
                    <v-container class="without-padding dense">
                        <v-row>
                            <v-col cols="12" sm="12" md="12">
                                <v-text-field v-model="editedItem.old_password"
                                              dense
                                              :disabled="isProcessing"
                                              @keyup.enter="save"
                                              label="Contraseña actual"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="12" md="12">
                                <v-text-field v-model="editedItem.new_password"
                                              dense
                                              :disabled="isProcessing"
                                              @keyup.enter="save"
                                              label="Nueva contraseña"></v-text-field>
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
                    <v-spacer></v-spacer>
                    <v-btn color="teal darken-'" text @click="save"
                           :disabled="isProcessing"
                    >
                        <v-icon left>mdi-content-save</v-icon>
                        <span>Cambiar contraseña</span>
                    </v-btn>
                    <v-btn color="blue darken-1" text @click="close"
                           :disabled="isProcessing">
                        <v-icon left>mdi-close</v-icon>
                        <span>Cancelar</span>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <div class="clear"></div>

        <v-progress-linear
                :active="progressBarActive"
                :indeterminate="true"
                :rounded="false"
                color="teal"
        ></v-progress-linear>
    </div>
</template>

<script>
    export default {
        name: "Topnav",
        data() {
            return {
                accountMenuItems: [
                    {icon: 'mdi-key-variant', title: 'Cambiar contraseña', action: 'changePassword'},
                    {icon: 'mdi-lock', title: 'Salir', action: 'logout'}
                ],
                accountValue: false,

                notificationsQty: 0,
                notificationsMenuItems: [
                    // {icon: 'mdi-message', content: 'El 11 de febrero se realizarán tareas de mantenimiento, favor tomar previsiones', action: 'notif1'},
                    // {icon: 'mdi-message', content: 'Debe actualizar los datos de sus planes de cuentas', action: 'profile'},
                ],
                notificationsValue: false,

                progressBarActive: false,

                // UI disable notifications, remove or set to false to enable them
                notificationsAreDisabled: true,

                isProcessing: false,
                editedItem: {
                    old_password: '',
                    new_password: ''
                },
                defaultItem: {
                    old_password: '',
                    new_password: ''
                },
                errors: [],
                showPassDialog: false,
            }
        },
        mounted() {

        },
        methods: {
            handleTopMenu(action) {
                switch (action) {
                    case 'logout':
                        document.getElementById('logout-form').submit();
                        break;
                    case  'changePassword':
                        this.showPassDialog = true;
                        this.errors = [];
                        this.isProcessing = false;
                        // this.editedItem = Object.assign({})
                        break;
                    default:
                }
            },
            updatePassword() {
                this.isProcessing = true;
                let editedIndex = this.editedIndex;
                axiosPutApi(`/users/password/${this.userEmail}`, this.editedItem, res => {
                    if (res.data.hasOwnProperty("errors") && res.data.errors.length > 0) {
                        this.errors = res.data.errors;
                        this.isProcessing = false;
                    } else {
                        this.isProcessing = true;
                        setTimeout(() => {
                            showSuccessMessage(res.data.okMessage);
                        }, 200)
                        this.close();
                    }
                });
            },
            save() {
                this.updatePassword();
            },
            close() {
                this.errors = [];
                this.showPassDialog = false;
                this.$nextTick(() => {
                    this.editedItem = Object.assign({}, this.defaultItem);
                    this.editedIndex = -1;
                });
            },
            handleNotifications(action) {
                switch (action) {
                    case 'first':
                        break;
                    default:
                }
            }
        },
        computed: {
            username: function () {
                return this.$store.getters.user.username;
            },
            userEmail: function () {
                return this.$store.getters.user.email;
            },
        }
    }
</script>

<style scoped>

</style>
