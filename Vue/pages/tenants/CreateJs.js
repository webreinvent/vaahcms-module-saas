let namespace = 'tenants';


import GlobalComponents from '../../vaahvue/helpers/GlobalComponents'

import AutoCompleteAjax from '../../vaahvue/reusable/AutoCompleteAjax'


export default {
    computed:{
        root() {return this.$store.getters['root/state']},
        permissions() {return this.$store.getters['root/state'].permissions},
        page() {return this.$store.getters[namespace+'/state']},
        ajax_url() {return this.$store.getters[namespace+'/state'].ajax_url},
        new_item() {return this.$store.getters[namespace+'/state'].new_item},
        new_item_errors() {return this.$store.getters[namespace+'/state'].new_item_errors},
    },
    components:{
        ...GlobalComponents,
        AutoCompleteAjax,
    },
    data()
    {
        return {
            is_content_loading: false,
            is_btn_loading: null,
            labelPosition: 'on-border',
            params: {},
            local_action: null,
            ajax_url_server_search: null,
        }
    },
    watch: {
        $route(to, from) {
            this.updateView()
        },
        'new_item.name': {
            deep: true,
            handler(new_val, old_val) {

                if(new_val)
                {
                    this.new_item.slug = this.$vaah.strToSlug(new_val);
                    this.updateNewItem();
                }

            }
        },
    },
    mounted() {

        //----------------------------------------------------
        this.ajax_url_server_search = this.ajax_url+'/server';
        //----------------------------------------------------
        this.onLoad();
        //----------------------------------------------------
        this.resetActiveItem();
        //----------------------------------------------------
        //----------------------------------------------------
    },
    methods: {
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
        update: function(name, value)
        {
            let update = {
                state_name: name,
                state_value: value,
                namespace: namespace,
            };
            this.$vaah.updateState(update);
        },
        //---------------------------------------------------------------------
        updateView: function()
        {
            this.$store.dispatch(namespace+'/updateView', this.$route);
        },
        //---------------------------------------------------------------------
        onLoad: function()
        {
            this.updateView();
            this.getAssets();
        },
        //---------------------------------------------------------------------
        async getAssets() {
            await this.$store.dispatch(namespace+'/getAssets');
        },
        //---------------------------------------------------------------------
        resetActiveItem: function()
        {
            this.update('active_item', null);
        },
        //---------------------------------------------------------------------
        updateServerId: function(option)
        {
            console.log('--->', option);
            this.new_item.vh_saas_server_id = option.id;

            if(option.host_type == 'CPanel-MySql')
            {
                this.new_item.database_name = option.meta.cpanel_username+'_';
                this.new_item.database_username = option.meta.cpanel_username+'_';
            }

            this.updateNewItem();
        },
        //---------------------------------------------------------------------
        create: function (action) {
            this.is_btn_loading = true;

            this.$Progress.start();

            this.params = {
                new_item: this.new_item,
                action: action
            };

            let url = this.ajax_url+'/create';
            this.$vaah.ajax(url, this.params, this.createAfter);
        },
        //---------------------------------------------------------------------
        createAfter: function (data, res) {
            this.is_btn_loading = false;
            this.$Progress.finish();

            if(data)
            {

                this.$emit('eReloadList');

                if(this.local_action === 'save-and-close')
                {
                    this.saveAndClose()
                }

                if(this.local_action === 'save-and-new')
                {
                    this.saveAndNew()
                }

                if(this.local_action === 'save-and-clone')
                {
                    this.saveAndClone()
                }

            }


        },
        //---------------------------------------------------------------------
        updateNewItem: function()
        {
            this.update('new_item', this.new_item);
        },
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
        setLocalAction: function (action) {
            this.local_action = action;
            this.create();
        },
        //---------------------------------------------------------------------
        saveAndClose: function () {
            this.update('active_item', null);
            this.$router.push({name:'tenants.list'});
        },
        //---------------------------------------------------------------------
        saveAndNew: function () {
            this.update('active_item', null);
            this.resetNewItem();
        },
        //---------------------------------------------------------------------
        saveAndClone: function () {
            this.fillNewItem();
            this.$router.push({name:'tenants.create'});
        },
        //---------------------------------------------------------------------
        getNewItem: function()
        {
            let new_item = {
                name: null,
                slug: null,
                is_active: null,
                details: null,
            };
            return new_item;
        },
        //---------------------------------------------------------------------
        resetNewItem: function()
        {
            let new_item = this.getNewItem();
            this.update('new_item', new_item);
        },
        //---------------------------------------------------------------------
        fillNewItem: function () {

            let new_item = {
                name: null,
                slug: null,
                is_active: null,
                details: null,
            };

            for(let key in new_item)
            {
                new_item[key] = this.new_item[key];
            }
            this.update('new_item', new_item);
        },
        //---------------------------------------------------------------------
        hasPermission: function(slug)
        {
            return this.$vaah.hasPermission(this.permissions, slug);
        },
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
    }
}
