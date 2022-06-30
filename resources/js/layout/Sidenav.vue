<template>
  <nav id="nav">
    <div id="nav-inner">
      <perfect-scrollbar :options="scrollbarOptions"
                         style="height: 100%">
        <div class="logo-wrapper">
          <router-link :to="{name: 'home'}" class="without-tooltip">
            <img class="logo" :src="logo" alt="Logo">
          </router-link>
        </div>
        <ul>
          <li>
            <router-link :to="{name: 'home'}" v-if="$can('route', 'home')">
              <v-icon left class="vertical-center">mdi-view-dashboard</v-icon>
              <span class="vertical-center">Inicio</span>
            </router-link>
          </li>
          <li>
            <router-link :to="{name: 'users'}" v-if="$can('route', 'users')">
              <v-icon left class="vertical-center">mdi-account-multiple</v-icon>
              <span class="vertical-center">Usuarios</span>
            </router-link>
          </li>
          <li class="dropdown" v-if="$can('route', 'permissions') || $can('route', 'roles')">
            <a>
              <v-icon left class="vertical-center">mdi-security</v-icon>
              <span class="vertical-center">Seguridad</span>
              <v-icon right class="vertical-center">mdi-chevron-right</v-icon>
            </a>
            <ul>
              <li>
                <router-link :to="{name: 'roles'}" v-if="$can('route', 'roles')">
                  <v-icon left class="vertical-center">mdi-account-badge-horizontal</v-icon>
                  <span class="vertical-center">Roles</span>
                </router-link>
              </li>
              <li>
                <router-link :to="{name: 'permissions'}" v-if="$can('route', 'permissions')">
                  <v-icon left class="vertical-center">mdi-security</v-icon>
                  <span class="vertical-center">Permisos</span>
                </router-link>
              </li>
            </ul>
          </li>
          <li>
            <router-link :to="{name: 'tasks'}" v-if="$can('route', 'tasks')">
              <v-icon left class="vertical-center">mdi-layers</v-icon>
              <span class="vertical-center">Tareas</span>
            </router-link>
          </li>
        </ul>
      </perfect-scrollbar>
    </div>
  </nav>
</template>

<script>
export default {
  props: ['logo'],
  name: "Sidenav",
  data() {
    return {
      scrollbarOptions: {
        // scrollYMarginOffset: 10
      }
    }
  },
  mounted() {
    this.traverseDropdowns();
  },
  methods: {
    // to activate the one that matches the current dropdown
    traverseDropdowns() {
      let curLocation = document.location.href;
      $("li.dropdown").each(
        function (i, e) {
          let links = $(e).find("ul li a");
          for (let i in links) {
            if (typeof (links[i]) == "object") {
              if (links[i].href == curLocation) {
                $(e).addClass("is-active");
                $(links[i]).parent().parent().slideDown();
              }
            }
          }
        }
      );
    },
    generateInventory() {
      this.isProcessing = true;
      // this.editedIndex = this.entries.indexOf(item);
      // this.editedItem = Object.assign({}, item);

      axiosPrintApi('/stocks/inventory', {}, res => {

        const url = window.URL.createObjectURL(new Blob([res.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'inventario.pdf'); //or any other extension
        document.body.appendChild(link);
        link.click();

        this.isProcessing = false;
      });
    },
  }
}
</script>

<style scoped>

</style>
