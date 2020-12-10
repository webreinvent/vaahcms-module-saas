<template>

    <b-menu>
        <b-menu-list label="Menu">
            <b-menu-item tag="router-link" to="/" label="Tenants"></b-menu-item>
            <b-menu-item tag="router-link" to="/tenantapps" label="Tenant's Apps"></b-menu-item>
            <b-menu-item tag="router-link" to="/apps" label="Apps"></b-menu-item>
            <b-menu-item tag="router-link" to="/servers" label="Servers"></b-menu-item>
        </b-menu-list>
    </b-menu>

</template>

<script>
export default {
    computed:{
        root() {return this.$store.getters['root/state']},
        assets() {return this.$store.getters['root/state'].assets},
    },
    components:{

    },
    data()
    {
        let obj = {
        };

        return obj;
    },
    watch: {


    },
    mounted() {
        //---------------------------------------------------------------------

    },
    methods: {
        //---------------------------------------------------------------------

        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
    }
}
</script>
