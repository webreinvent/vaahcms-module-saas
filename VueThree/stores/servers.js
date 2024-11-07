import {VaahHelper as Vaah} from "../../vaahvue/helpers/VaahHelper";

//---------Variables
let base_url = document.getElementsByTagName('base')[0].getAttribute("href");
let debug = document.getElementById('debug').getAttribute('content');
//---------/Variables

let ajax_url = base_url+"/saas/servers";

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
            name: null,
            slug: null,
            host_type: null,
            driver: null,
            host: null,
            port: null,
            username: null,
            password: null,
            sslmode: null,
            count_tenants: null,
            count_db_instances: null,
            is_active: null,
            meta: {
                cpanel_domain: null,
                cpanel_api_token: null,
                cpanel_username: null,
                protocol: 'https',
                port: 2083,
                ssl_key_path: null,
                ssl_cert_path: null,
                ssl_ca_path: null,
            }
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

            if(payload && payload.name && payload.name == 'servers.list')
            {
                list_view = 'large';

                update = {
                    key: 'active_item',
                    value: null
                };

                commit('updateState', update);

            }

            if(payload.name == 'servers.create'
            || payload.name == 'servers.view'
            || payload.name == 'servers.edit')
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
