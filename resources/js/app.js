import Vue from 'vue'; // window.Vue = require('vue');

// plugins
import Vuetify from 'vuetify';
import VueToasted from 'vue-toasted';
import PerfectScrollbar from 'vue2-perfect-scrollbar';
import 'vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css'
import VueRouter from 'vue-router';
import {store} from './store/index.js';

import {abilitiesPlugin} from '@casl/vue';
// abilities (to not use a file)
import {defineAbility} from '@casl/ability';

// bootstrap config, helpers import
require('./bootstrap');
require('./axioswrapper');
require('./helpers');

// another files
require('./dashboard');

// plugins options
const vuetifyOpts = {
  theme: {
    themes: {
      light: {
        primary: '#3f51b5',
        secondary: '#b0bec5',
        accent: '#8c9eff',
        error: '#b71c1c',
      },
      dark: {
        primary: '#3f51b5',
        secondary: '#b0bec5',
        accent: '#8c9eff',
        error: '#b71c1c',
      }
    },
  },
};
Vue.use(Vuetify);
Vue.use(VueToasted, {
  // iconPack. material|fontawesome|custom-class
  iconPack: 'mdi',
  containerClass: 'custom-toasted'
});
Vue.use(PerfectScrollbar);
Vue.use(VueRouter);

const ability = defineAbility((can, cannot) => {
});
Vue.use(abilitiesPlugin, ability);

// global components
Vue.component('sidenav', require('./layout/Sidenav').default);
Vue.component('topnav', require('./layout/Topnav').default);

// views
import NotFound from './views/NotFound';
import Home from './views/Home';
import Users from './views/Users';
import Roles from './views/Roles';
import Tasks from './views/Tasks';
import Permissions from './views/Permissions';
import Companies from './views/Companies';
import Components from './views/Components';

store.dispatch('getUser').then(() => {
  const allowedRoutes = store.getters.user.routes ? store.getters.user.routes : [];
  allowedRoutes.push('notfound'); // add certain routes
  const permissions = store.getters.user.permissions ? store.getters.user.permissions : [];
  ability.update(permissions);
// the routes
  const router = new VueRouter({
    routes: [
      {
        path: '/home', name: 'home', component: Home,
        meta: {breadcrumb: "Inicio",},
      },
      {
        path: '/users', name: 'users', component: Users,
        meta: {breadcrumb: "Usuarios",}
      },
      {
        path: '/tasks', name: 'tasks', component: Tasks,
        meta: {breadcrumb: "Tareas",}
      },
      {
        path: '/permissions', name: 'permissions', component: Permissions,
        meta: {breadcrumb: "Permisos",}
      },
      {
        path: '/roles', name: 'roles', component: Roles,
        meta: {breadcrumb: "Roles",}
      },
      {
        path: '/components', name: 'components', component: Components,
        meta: {breadcrumb: "Componentes",}
      },
      {
        path: '*', name: 'notfound', component: NotFound,
        meta: {breadcrumb: "Página no encontrada",}
      },
    ],
    mode: 'history',
    scrollBehavior() {
      return {x: 0, y: 0}
    }
  });

  router.beforeEach((to, from, next) => {
    // search for resource string in the "name" of the element
    if (allowedRoutes.indexOf(to.name) !== -1) {
      next(); // dejamos pasar
    } else {
      next('/404'); // mandamos a una ruta ficticia
    }
  });

// the instance
  if ($("#app").length) {
    const app = new Vue({
      vuetify: new Vuetify(vuetifyOpts),
      router,
      store,
      el: '#app',
      data() {
        return {
          emailsNumber: 0,
        }
      },
      created() {
        window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.$store.getters.user.api_token;
      },
      mounted() {
        initDashboard();
      },
      methods: {
        addEmail() {
          console.log("Trying to add an email");
          this.emailsNumber += 1;
        },
        testJquery() {
          console.log("There are " + $("div").length + " divs");
          showInfoMessage("There are " + $("div").length + " divs");
        },
        testAxios() {
          showErrorMessage('Axios just started to make the request');
          axiosGet('http://apicors.test/api/users', (response) => {
            showSuccessMessage('We received the data, it has ' + response.data.length + ' records');
          });
        }
      }
    });
  }
});

// if its the login
if ($(".login-wrapper").length) {
  window.onload = init();

  function init() {
    hidePreloader();
    setTimeout(function () {
      $("#username").focus();
    }, 500);
  }

  function hidePreloader() {
    $("#outer-loader").fadeOut(400, function () {
      // after hide loader we remove the preloader
      $(this).children(".preloader-wrapper").remove();
      // after hide loader we show the main-wrapper
      $(".main-wrapper").slideDown(300);
      // after hide loader we start the sidenav
      // this start works for mobile
    });
  }
}

