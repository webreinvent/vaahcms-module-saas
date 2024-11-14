let routes= [];
let routes_list= [];

import List from '../pages/tenants/List.vue'
import Form from '../pages/tenants/Form.vue'
import Item from '../pages/tenants/Item.vue'
import Filters from '../pages/tenants/Filters.vue'

routes_list = {

    path: '/tenants',
    name: 'tenants.index',
    component: List,
    props: true,
    children:[
        {
            path: 'form/:id?',
            name: 'tenants.form',
            component: Form,
            props: true,
        },
        {
            path: 'view/:id?',
            name: 'tenants.view',
            component: Item,
            props: true,
        },
        {
            path: 'filters',
            name: 'tenants.filters',
            component: Filters,
            props: true,
        },

    ]
};

routes.push(routes_list);

export default routes;

