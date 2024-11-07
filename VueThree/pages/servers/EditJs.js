let namespace = 'servers';

export default {
    props: ['id'],
    computed:{
        root() {return this.$store.getters['root/state']},
        page() {return this.$store.getters[namespace+'/state']},
        ajax_url() {return this.$store.getters[namespace+'/state'].ajax_url},
        item() {return this.$store.getters[namespace+'/state'].active_item},
    },
    components:{
    },
    data()
    {
        return {
            is_content_loading: false,
            is_btn_loading: null,
            is_btn_loading_connect: null,
            labelPosition: 'on-border',
            params: {},
            local_action: null,
            title: null,
            new_password: null,
        }
    },
    watch: {
        $route(to, from) {
            this.updateView()
        },
        'item.name': {
            deep: true,
            handler(new_val, old_val) {

                if(new_val)
                {
                    this.item.slug = this.$vaah.strToSlug(new_val);

                }

            }
        },
        'new_password': {
            deep: true,
            handler(new_val, old_val) {

                if(new_val)
                {
                    this.item.password = new_val;
                }

            }
        },
    },
    mounted() {
        //----------------------------------------------------
        this.onLoad();
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
            this.is_content_loading = true;

            this.updateView();
            this.getAssets();
            this.getItem();
        },
        //---------------------------------------------------------------------
        async getAssets() {
            await this.$store.dispatch(namespace+'/getAssets');
        },
        //---------------------------------------------------------------------
        getItem: function () {
            this.$Progress.start();
            this.params = {};
            let url = this.ajax_url+'/item/'+this.$route.params.id;
            this.$vaah.ajaxGet(url, this.params, this.getItemAfter);
        },
        //---------------------------------------------------------------------
        getItemAfter: function (data, res) {
            this.$Progress.finish();
            this.is_content_loading = false;

            if(data)
            {
                this.title = data.name;
                this.update('active_item', data);
            } else
            {
                //if item does not exist or delete then redirect to list
                this.update('active_item', null);
                this.$router.push({name: 'servers.list'});
            }
        },
        //---------------------------------------------------------------------
        store: function () {
            this.$Progress.start();

            let params = {
                item: this.item,
            };

            let url = this.ajax_url+'/store/'+this.item.id;
            this.$vaah.ajax(url, params, this.storeAfter);
        },
        //---------------------------------------------------------------------
        storeAfter: function (data, res) {

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

                if(this.local_action === 'save')
                {
                    this.$router.push({name: 'servers.view', params:{id:this.id}});
                }

            }

        },
        //---------------------------------------------------------------------
        setLocalAction: function (action) {
            this.local_action = action;
            this.store();
        },
        //---------------------------------------------------------------------
        saveAndClose: function () {
            this.update('active_item', null);
            this.$router.push({name:'servers.list'});
        },
        //---------------------------------------------------------------------
        saveAndNew: function () {
            this.update('active_item', null);
            this.resetNewItem();
            this.$router.push({name:'servers.create'});
        },
        //---------------------------------------------------------------------
        saveAndClone: function () {
            this.fillNewItem();
            this.update('active_item', null);
            this.$router.push({name:'servers.create'});
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
        //---------------------------------------------------------------------
        connect: function () {
            this.is_btn_loading_connect = true;

            this.$Progress.start();

            this.params = {
                new_item: this.item
            };

            let url = this.ajax_url+'/connect';
            this.$vaah.ajax(url, this.params, this.connectAfter);
        },
        //---------------------------------------------------------------------
        connectAfter: function (data, res) {
            this.is_btn_loading_connect = false;
            this.$Progress.finish();
        },
        //---------------------------------------------------------------------
    }
}
