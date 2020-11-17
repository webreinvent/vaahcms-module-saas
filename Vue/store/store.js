import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

import root from './modules/root';
import servers from './modules/servers';
import tenants from './modules/tenants';

export const store = new Vuex.Store({
    modules: {
        root: root,
        servers: servers,
        tenants: tenants,
    }
});
