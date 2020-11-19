import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

import root from './modules/root';
import tenants from './modules/tenants';
import apps from './modules/apps';
import servers from './modules/servers';

export const store = new Vuex.Store({
    modules: {
        root: root,
        servers: servers,
        apps: apps,
        tenants: tenants,
    }
});
