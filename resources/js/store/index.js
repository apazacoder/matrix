import 'es6-promise/auto'; // polyfill requerido para Promises
import Vue from 'vue';
import Vuex from 'vuex';
import user from './modules/user';
Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {},
    getters : {},
    mutations: {},
    actions:{},

    modules : {
        user
    }
});


