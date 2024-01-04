import {VaahHelper as Vaah} from "../../vaahvue/helpers/VaahHelper";

//---------Variables
let base_url = document.getElementsByTagName('base')[0].getAttribute("href");
let current_url = document.getElementById('current_url').getAttribute('content');
let debug = document.getElementById('debug').getAttribute('content');
//---------/Variables

let ajax_url = base_url+'/saas';

export default {
    namespaced: true,
    //=========================================================================
    state: {
        debug: debug,
        base_url: base_url,
        current_url: current_url,
        ajax_url: ajax_url,
        assets: null,
        assets_reload: null,
        permissions: null,
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

            let root_assets = state.assets;

            if(!state.assets || state.assets_reload == true)
            {

                let params = {};

                let url = state.ajax_url + '/assets';
                let data = await Vaah.ajax(url, params);

                if (!root_assets) {
                    root_assets = {};
                }

                for (let index in data.data.data) {
                    root_assets[index] = data.data.data[index];
                }

                let payload = {
                    key: 'assets',
                    value: root_assets
                };

                this.commit('root/updateState', payload);

                payload = {
                    key: 'assets_reload',
                    value: false
                };

                this.commit('root/updateState', payload);

            }

        },
        //---------------------------------------------------------------------
        localTimeShortFormat: function (value) {

            const utcTime = new Date(value)

            const date = utcTime.getDate();
            const dateYear = utcTime.getFullYear();

            const current = new Date()

            const currentDate = current.getDate();
            const currentYear = current.getFullYear();

            if (date === currentDate) {
                return utcTime.toLocaleTimeString();
            } else if (dateYear === currentYear) {

                return utcTime.toLocaleString('default', { month: 'short' })+
                    ' '+utcTime.getDate();
            } else {
                return utcTime.toLocaleString('default', { month: 'short' })+
                    ' '+utcTime.getDate()+
                    ' '+utcTime.getFullYear();
            }


        },

        //-----------------------------------------------------------------
        reloadAssets: function ({ state, commit, dispatch, getters }) {
            let payload = {
                key: 'assets_reload',
                value: true
            };
            commit('updateState', payload);
            dispatch('getAssets');
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
