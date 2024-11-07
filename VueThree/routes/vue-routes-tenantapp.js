let routes= [];
let routes_list= [];

import List from '../pages/tenantapp/List.vue'
import Form from '../pages/tenantapp/Form.vue'
import Item from '../pages/tenantapp/Item.vue'
import Filters from '../pages/tenantapp/Filters.vue'

routes_list = {

    path: '/tenantapp',
    name: 'tenantapp.index',
    component: List,
    props: true,
    children:[
        {
            path: 'form/:id?',
            name: 'tenantapp.form',
            component: Form,
            props: true,
        },
        {
            path: 'view/:id?',
            name: 'tenantapp.view',
            component: Item,
            props: true,
        },
        {
            path: 'filters',
            name: 'tenantapp.filters',
            component: Filters,
            props: true,
        },

    ]
};

routes.push(routes_list);

export default routes;

