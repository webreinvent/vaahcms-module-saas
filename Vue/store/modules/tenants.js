import {VaahHelper as Vaah} from "../../vaahvue/helpers/VaahHelper";

//---------Variables
let base_url = document.getElementsByTagName('base')[0].getAttribute("href");
let debug = document.getElementById('debug').getAttribute('content');
//---------/Variables

let ajax_url = base_url+"/saas/tenants";

export default {
    namespaced: true,
    state: {
        debug: debug,
        ajax_url: ajax_url,
        assets: null,
        assets_is_fetching: null,
        list: null,
        list_is_empty: false,
        is_list_loading: false,
        list_view: true,
        active_item: null,
        is_item_loading: false,
        show_filters: false,
        query_string: {
            page: 1,
            q: null,
            trashed: null,
            filter: null,
            sort_by: null,
            sort_order: 'desc',
            search_by: null,
        },
        bulk_action:{
            selected_items: [],
            data: {},
            action: null,
        },
        new_item:{
            vh_saas_server_id: null,
            name: null,
            slug: null,
            path: null,
            domain: null,
            sub_domain: null,
            database_name: null,
            database_username: null,
            database_password: null,
            database_sslmode: null,
            database_charset: 'utf8mb4',
            database_collation: 'utf8mb4_unicode_ci',
            is_active: null,
            notes: null,
            meta: {

                ssl_key_path: null,
                ssl_cert_path: null,
                ssl_ca_path: null,

            },
        },

    },
    //=========================================================================
    mutations:{
        updateState: function (state, payload) {
            state[payload.key] = payload.value;
        },
        //-----------------------------------------------------------------
    },
    //=========================================================================
    actions:{
        //-----------------------------------------------------------------
        async getAssets({ state, commit, dispatch, getters }) {

            if(!state.assets_is_fetching || !state.assets)
            {
                let payload = {
                    key: 'assets_is_fetching',
                    value: true
                };
                commit('updateState', payload);

                let url = state.ajax_url+'/assets';

                console.log('--->assets url', url);

                let params = {};
                let data = await Vaah.ajaxGet(url, params);
                payload = {
                    key: 'assets',
                    value: data.data.data
                };

                commit('updateState', payload);
            }

        },
        //-----------------------------------------------------------------
        updateView({ state, commit, dispatch, getters }, payload) {
            let list_view;
            let update;

            if(payload && payload.name && payload.name == 'tenants.list')
            {
                list_view = 'large';

                update = {
                    key: 'active_item',
                    value: null
                };

                commit('updateState', update);

            }

            if(payload.name == 'tenants.create'
            || payload.name == 'tenants.view'
            || payload.name == 'tenants.edit')
            {
                list_view = 'small';
            };

            let view = {
                key: 'list_view',
                value: list_view
            };

            commit('updateState', view);

        },
        //-----------------------------------------------------------------
    },
    //=========================================================================
    getters:{
        state(state) {return state;},
        assets(state) {return state.assets;},
        permissions(state) {return state.permissions;},
    }

}
