let routes= [];
let routes_list= [];

import List from '../pages/tenantapps/List.vue'
import Form from '../pages/tenantapps/Form.vue'
import Item from '../pages/tenantapps/Item.vue'
import Filters from '../pages/tenantapps/Filters.vue'

routes_list = {

    path: '/tenantapps',
    name: 'tenantapps.index',
    component: List,
    props: true,
    children:[
        {
            path: 'form/:id?',
            name: 'tenantapps.form',
            component: Form,
            props: true,
        },
        {
            path: 'view/:id?',
            name: 'tenantapps.view',
            component: Item,
            props: true,
        },
        {
            path: 'filters',
            name: 'tenantapps.filters',
            component: Filters,
            props: true,
        },

    ]
};

routes.push(routes_list);

export default routes;

