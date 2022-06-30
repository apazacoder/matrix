<template>
  <div id="main">
    <div class="title-section"></div>
    <v-card>
      <v-data-table
        class="elevation-1 centered-th centered-td three-actions"
        loading-text="Cargando... Espere por favor"
        dense
        :headers="headers"
        :items="users"
        :loading="isProcessing"
        :footer-props="tableFooterProps"
        :options="tableOptions"
        :search="search"
      >
        <template v-slot:item.text_roles="{ item }">
                  <span v-for="role in item.roles">
                    <v-chip v-if="role.name=='partner'"
                            small color="indigo lighten-1" text-color="white">
                        <span v-text="role.text"></span>
                    </v-chip>
                    <v-chip v-else-if="role.name=='attorney'"
                            v-text="role.text"
                            small color="blue" text-color="white">
                        <span v-text="role.text"></span>
                    </v-chip>
                    <v-chip v-else-if="role.name=='suspended'"
                            v-text="role.text"
                            small color="red lighten-1" text-color="white">
                        <span v-text="role.text"></span>
                    </v-chip>
                    <v-chip v-else v-text="role.text"></v-chip>
                  </span>
        </template>
        <template v-slot:item.warehousesunit="{ item }">
          <span v-if="item.warehousesunit" v-text="item.warehousesunit.name"></span>
        </template>
        <template v-slot:top>
          <v-toolbar flat color="white">
            <v-toolbar-title>Usuarios</v-toolbar-title>
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
            <v-dialog v-model="dialog" content-class="v-dialog-dense v-dialog-medium">
              <template v-slot:activator="{ on }">
                <v-btn class="btn-green" v-on="on" @click="showForAction('create')">
                  <v-icon left>mdi-plus</v-icon>
                  <span>Crear usuario</span>
                </v-btn>
              </template>
              <v-card>
                <v-card-title>
                  <span class="headline">{{ formTitle }}</span>
                </v-card-title>
                <v-card-text>
                  <v-container class="without-padding dense">
                    <v-row>
                      <v-col cols="12" sm="12" md="12">
                        <v-text-field v-model="editedItem.email"
                                      dense @keyup.enter="save"
                                      :disabled="editionIsDisabled"
                                      label="Correo electrónico"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field v-model="editedItem.first_name"
                                      dense @keyup.enter="save"
                                      :disabled="editionIsDisabled"
                                      label="Primer nombre"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field v-model="editedItem.second_name"
                                      dense @keyup.enter="save"
                                      :disabled="editionIsDisabled"
                                      label="Segundo nombre"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field v-model="editedItem.first_surname"
                                      dense @keyup.enter="save"
                                      :disabled="editionIsDisabled"
                                      label="Primer apellido"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field v-model="editedItem.second_surname"
                                      dense @keyup.enter="save"
                                      :disabled="editionIsDisabled"
                                      label="Segundo apellido"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field v-model="editedItem.ci"
                                      dense @keyup.enter="save"
                                      :disabled="editionIsDisabled"
                                      label="Cédula de Identidad"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field v-model="editedItem.exp"
                                      dense @keyup.enter="save"
                                      :disabled="editionIsDisabled"
                                      label="Expedido en"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="12" md="12">
                        <v-text-field v-model="editedItem.position"
                                      dense @keyup.enter="save"
                                      :disabled="editionIsDisabled"
                                      label="Cargo"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="12" md="12">
                        <v-text-field v-model="editedItem.password"
                                      v-if="currentAction == 'create' || currentAction == 'update'"
                                      dense @keyup.enter="save"
                                      :disabled="editionIsDisabled"
                                      :label="(currentAction == 'update' ? 'Nueva ': '') + 'Contraseña'"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="12" md="12">
                        <v-autocomplete
                          v-model="editedItem.warehousesunit_id"
                          :items="warehousesunits"
                          item-text="name"
                          item-value="id"
                          :disabled="editionIsDisabled"
                          label="Unidad de Almacén"
                          ref="warehouseunit"
                          no-data-text="No hay coincidencias"
                        ></v-autocomplete>
                      </v-col>

                      <v-col cols="12" sm="12" md="12">Roles del usuario</v-col>
                      <v-col cols="12" sm="4" md="4"
                             v-for="(role, i) in roles" :key="i">
                        <v-checkbox v-model="editedItem.roles"
                                    :label="role.text"
                                    :disabled="editionIsDisabled"
                                    :value="role"
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
            v-if="$can('read', 'users')"
            class="mr-2"
            :disabled="isProcessing"
            @click="showForAction('read', item)">
            mdi-eye
          </v-icon>
          <v-icon
            :color="updateColor"
            v-if="$can('update', 'users')"
            class="mr-2"
            :disabled="isProcessing"
            @click="showForAction('update', item)">
            mdi-pencil
          </v-icon>
          <v-icon
            :color="deleteColor"
            v-if="$can('delete', 'users')"
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
      {text: 'Correo electrónico', align: 'start', value: 'email'},
      {text: 'Primer nombre', value: 'first_name'},
      {text: 'Segundo nombre', value: 'second_name'},
      {text: 'Primer apellido', value: 'first_surname'},
      {text: 'Segundo apellido', value: 'second_surname'},
      {text: 'C.I.', value: 'ci'},
      {text: 'Expedido en', value: 'exp'},
      {text: 'Cargo', value: 'position'},
      {text: 'Unidad de almacén', value: 'warehousesunit'},
      {text: 'Roles', value: 'text_roles', sortable: false},
      {text: 'Acciones', value: 'actions', sortable: false},
    ],
    users: [],
    editedIndex: -1, // -1: para crear usuario, cualquier otro para editar usuario
    editedItem: {
      email: '',
      first_name: '',
      second_name: '',
      first_surname: '',
      second_surname: '',
      ci: '',
      exp: '',
      position: '',
      warehousesunit_id: '',
      created_at: '',
      updated_at: '',
      roles: [],
    },
    defaultItem: {
      email: '',
      first_name: '',
      second_name: '',
      first_surname: '',
      second_surname: '',
      ci: '',
      exp: '',
      position: '',
      warehousesunit_id: '',
      created_at: '',
      updated_at: '',
      roles: [],
    },

    roles: [],
    errors: [],
    currentAction: '',

    isProcessing: true,

    // relations
    warehousesunits: [],

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
          return 'Crear usuario';
          break;
        case 'read':
          return 'Ver usuario';
          break;
        case 'update':
          return 'Editar usuario';
          break;
        case 'delete':
          return 'Borrar usuario';
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
      this.getUsers();
    },
    // index users
    getUsers(callback = null) {
      this.isProcessing = true;
      axiosGetApi('/users', res => {
        this.users = res.data.users;
        this.roles = res.data.roles;
        this.warehousesunits = res.data.warehousesunits;
        if (callback) {
          callback();
        }
        this.isProcessing = false;
      });
    },

    // update users
    updateUser() {
      this.isProcessing = true;
      let editedIndex = this.editedIndex;
      axiosPutApi(`/users/${this.editedItem.id}`, this.editedItem, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length > 0) {
          this.errors = res.data.errors;
          this.isProcessing = false;
        } else {
          this.getUsers(
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
      let rowIndex = editedIndex % $("tbody tr").length + 1;
      $("tbody tr:nth-child(" + rowIndex + ")").addClass('updated-row');
    },

    storeUser(allOk = null) {
      this.isProcessing = true;

      axiosPostApi('/users', this.editedItem, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length > 0) {
          this.errors = res.data.errors;
          this.isProcessing = false;
        } else {
          this.getUsers(
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

    destroyUser() {
      this.isProcessing = true;

      axiosDeleteApi(`/users/${this.editedItem.id}`, {}, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length > 0) {
          this.errors = res.data.errors;
          this.isProcessing = false;
        } else {
          this.getUsers(
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
      if (!item) {
        item = this.defaultItem;
      }
      this.editedIndex = this.users.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },
    close() {
      this.errors = [];
      this.dialog = false

      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      })
    },

    save() {
      switch (this.currentAction) {
        case 'create':
          this.storeUser();
          break;
        case 'read':
          this.close();
          break;
        case 'update':
          this.updateUser();
          break;
        case 'delete':
          this.destroyUser();
          break;
      }
    },
  },
}
</script>

<style scoped>

</style>
