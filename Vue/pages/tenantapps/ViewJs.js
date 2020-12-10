let namespace = 'tenantapps';

import GlobalComponents from '../../vaahvue/helpers/GlobalComponents'

import TableTrView from '../../vaahvue/reusable/TableTrView'
import TableTrActedBy from '../../vaahvue/reusable/TableTrActedBy'
import TableTrTag from '../../vaahvue/reusable/TableTrTag'
import TableTrStatus from '../../vaahvue/reusable/TableTrStatus'
import TableTrUrl from "../../vaahvue/reusable/TableTrUrl";
import TableTrDateTime from "../../vaahvue/reusable/TableTrDateTime";


export default {
    computed:{
        root() {return this.$store.getters['root/state']},
        permissions() {return this.$store.getters['root/state'].permissions},
        page() {return this.$store.getters[namespace+'/state']},
        ajax_url() {return this.$store.getters[namespace+'/state'].ajax_url},
        item() {return this.$store.getters[namespace+'/state'].active_item},
    },
    components:{
        ...GlobalComponents,
        TableTrView,
        TableTrStatus,
        TableTrActedBy,
        TableTrTag,
        TableTrUrl,
        TableTrDateTime,

    },
    data()
    {
        return {
            is_btn_loading: false,
            is_content_loading: false,
            database_action: null,
        }
    },
    watch: {
        $route(to, from) {

            if (to.query.page) {
                this.updateView();
                this.getItem();
            }

        }
    },
    mounted() {
        //----------------------------------------------------
        this.onLoad();
        //----------------------------------------------------
        this.$root.$on('eReloadItem', this.getItem);
        //----------------------------------------------------
        this.$root.$on('eResetBulkActions', this.resetBulkAction);
        //----------------------------------------------------
        //----------------------------------------------------
    },
    methods: {
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

            if(data && data)
            {
                if(data.is_active == 1){
                    data.is_active = 'Yes';
                }else{
                    data.is_active = 'No';
                }
                this.update('active_item', data);
            } else
            {
                //if item does not exist or delete then redirect to list
                this.update('active_item', null);
                this.$router.push({name: 'tenantapps.list'});
            }
        },
        //---------------------------------------------------------------------
        actions: function (action) {

            console.log('--->action', action);

            this.$Progress.start();
            this.page.bulk_action.action = action;
            this.update('bulk_action', this.page.bulk_action);
            let params = {
                inputs: [this.item.id],
                data: null
            };

            let url = this.ajax_url+'/actions/'+this.page.bulk_action.action;
            this.$vaah.ajax(url, params, this.actionsAfter);

        },
        //---------------------------------------------------------------------
        actionsAfter: function (data, res) {
            let action = this.page.bulk_action.action;
            if(data)
            {
                this.resetBulkAction();
                this.$emit('eReloadList');

                if(action == 'bulk-delete')
                {
                    this.$router.push({name: 'tenantapps.list'});
                } else
                {
                    this.getItem();
                }

            } else
            {
                this.$Progress.finish();
            }
        },
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
        resetBulkAction: function()
        {
            this.page.bulk_action = {
                selected_items: [],
                data: {},
                action: "",
            };
            this.update('bulk_action', this.page.bulk_action);
        },
        //---------------------------------------------------------------------
        confirmDelete: function()
        {
            let self = this;
            this.$buefy.dialog.confirm({
                title: 'Deleting record',
                message: 'Are you sure you want to <b>delete</b> ' +
                    'the record? This action cannot be undone.',
                confirmText: 'Delete',
                type: 'is-danger',
                hasIcon: true,
                onConfirm: function () {
                    self.actions('bulk-delete');
                }
            })
        },
        //---------------------------------------------------------------------
        isCopiable: function (label) {

            if(
                label == 'id' || label == 'uuid' || label == 'slug'
            )
            {
                return true;
            }

            return false;

        },
        //---------------------------------------------------------------------
        isUpperCase: function (label) {

            if(
                label == 'id' || label == 'uuid'
            )
            {
                return true;
            }

            return false;

        },
        //---------------------------------------------------------------------
        resetActiveItem: function () {
            this.update('active_item', null);
            this.$router.push({name:'tenantapps.list'});
        },
        //---------------------------------------------------------------------
        hasPermission: function(slug)
        {
            return this.$vaah.hasPermission(this.permissions, slug);
        },
        //---------------------------------------------------------------------
        confirmDatabaseCreate() {
            let self = this;
            this.$buefy.dialog.confirm({
                title: 'Create Database',
                message: 'Are you sure you to create <b>'+this.item.tenant.database_name
                    +'</b> database user to on <b>'+this.item.tenant.server.name+'</b> server?',
                confirmText: 'Create',
                type: 'is-info',
                hasIcon: true,
                onConfirm:function () {

                    self.database_action = 'create';
                    self.databaseActions();

                }
            })
        },
        //---------------------------------------------------------------------
        confirmDatabaseUserCreate() {
            let self = this;
            this.$buefy.dialog.confirm({
                title: 'Create Database User',
                message: 'Are you sure you want give access of <b>'+this.item.tenant.database_name
                    +'</b> database to user <b>'+this.item.tenant.database_username
                    +'</b> on <b>'+this.item.tenant.server.name+'</b> server?',
                confirmText: 'Create User',
                type: 'is-info',
                hasIcon: true,
                onConfirm:function () {

                    self.database_action = 'create-user';
                    self.databaseActions();

                }
            })
        },
        //---------------------------------------------------------------------
        confirmAssignDatabaseUser() {
            let self = this;
            this.$buefy.dialog.confirm({
                title: 'Assign User to Database',
                message: 'Are you sure? Database name <b>'+this.item.tenant.database_name
                    +'</b> will be <b>created</b> on <b>'+this.item.tenant.server.name+'</b> server?',
                confirmText: 'Create',
                type: 'is-info',
                hasIcon: true,
                onConfirm:function () {

                    self.database_action = 'assign-user';
                    self.databaseActions();

                }
            })
        },
        //---------------------------------------------------------------------
        confirmDatabaseUserDelete() {
            let self = this;
            this.$buefy.dialog.confirm({
                title: 'Delete Database User',
                message: 'Are you sure? Database username <b>'+this.item.tenant.database_username
                    +'</b> will be <b>deleted</b> on <b>'+this.item.tenant.server.name+'</b> server?',
                confirmText: 'Delete',
                type: 'is-danger',
                hasIcon: true,
                onConfirm:function () {

                    self.database_action = 'delete-user';
                    self.databaseActions();

                }
            })
        },
        //---------------------------------------------------------------------
        confirmDatabaseDelete() {
            let self = this;
            this.$buefy.dialog.confirm({
                title: 'Delete Database',
                message: 'Are you sure? Database name <b>'+this.item.tenant.database_name
                    +'</b> will be <b>delete</b> on <b>'+this.item.tenant.server.name+'</b> server?',
                confirmText: 'Create',
                type: 'is-danger',
                hasIcon: true,
                onConfirm:function () {

                    self.database_action = 'delete';
                    self.databaseActions();

                }
            })
        },
        //---------------------------------------------------------------------
        confirmUpdate() {
            let self = this;
            this.$buefy.dialog.confirm({
                title: 'Update App',
                message: 'Are you sure? Update will run <b>migration & seed</b> on ' +
                    '<b>'+this.item.tenant.name+'</b> database?',
                confirmText: 'Update',
                type: 'is-danger',
                hasIcon: true,
                onConfirm:function () {

                    self.database_action = 'update';
                    self.databaseActions();

                }
            })
        },
        //---------------------------------------------------------------------
        confirmMigration() {
            let self = this;
            this.$buefy.dialog.confirm({
                title: 'Migrating database',
                message: 'Are you sure you want to run <b>migration</b> on ' +
                    '<b>'+this.item.tenant.name+'</b> database?',
                confirmText: 'Run Migration',
                type: 'is-danger',
                hasIcon: true,
                onConfirm:function () {

                    self.database_action = 'migrate';
                    self.databaseActions();

                }
            })
        },

        //---------------------------------------------------------------------
        confirmRollback() {
            let self = this;
            this.$buefy.dialog.confirm({
                title: 'Delete Database',
                message: 'Are you sure? The Database <b>'+this.item.tenant.database_name+'</b> will be' +
                    ' <b>altered</b> on <b>'+this.item.tenant.server.name+'</b> server?',
                confirmText: 'Create',
                type: 'is-danger',
                hasIcon: true,
                onConfirm:function () {

                    self.database_action = 'rollback';
                    self.databaseActions();

                }
            })
        },
        //---------------------------------------------------------------------
        confirmSeed() {
            let self = this;
            this.$buefy.dialog.confirm({
                title: 'Migrating database',
                message: 'Are you sure you want to run <b>seeds</b> on <b>'+this.item.tenant.name+'</b> database?',
                confirmText: 'Run Migration',
                type: 'is-info',
                hasIcon: true,
                onConfirm:function () {

                    self.database_action = 'seed';
                    self.databaseActions();

                }
            })
        },

        //---------------------------------------------------------------------
        confirmInsertSampleData() {
            let self = this;
            this.$buefy.dialog.confirm({
                title: 'Insert Sample Data',
                message: 'Are you sure? This will insert <b>dummy data</b> on <b>'+this.item.tenant.name+'</b> database?',
                confirmText: 'Insert Dummy Data',
                type: 'is-info',
                hasIcon: true,
                onConfirm:function () {

                    self.database_action = 'insert-sample-data';
                    self.databaseActions();

                }
            })
        },
        //---------------------------------------------------------------------
        confirmWipeData() {
            let self = this;
            this.$buefy.dialog.confirm({
                title: 'Wipe Data',
                message: 'Are you sure? This will drop all the tables on <b>'+this.item.tenant.name+'</b> database?',
                confirmText: 'Delete All Data',
                type: 'is-danger',
                hasIcon: true,
                onConfirm:function () {

                    self.database_action = 'wipe';
                    self.databaseActions();

                }
            })
        },
        //---------------------------------------------------------------------
        databaseActions: function () {

            if(!this.database_action)
            {
                this.$buefy.toast.open('Database action not set');
                return false;
            }

            this.$Progress.start();
            let params = {};
            let url = this.ajax_url+'/item/'+this.item.id+'/database/actions/'+this.database_action;
            this.$vaah.ajax(url, params, this.databaseActionsAfter);
        },
        //---------------------------------------------------------------------
        databaseActionsAfter: function (data, res) {
            this.$Progress.finish();
            this.database_action = null;
            if(data)
            {
                this.getItem();
            }
        },
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
    }
}
