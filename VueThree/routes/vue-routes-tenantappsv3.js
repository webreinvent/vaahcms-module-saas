let routes= [];
let routes_list= [];

import List from '../pages/tenantappsv3/List.vue'
import Form from '../pages/tenantappsv3/Form.vue'
import Item from '../pages/tenantappsv3/Item.vue'
import Filters from '../pages/tenantappsv3/Filters.vue'

routes_list = {

    path: '/tenantappsv3',
    name: 'tenantappsv3.index',
    component: List,
    props: true,
    children:[
        {
            path: 'form/:id?',
            name: 'tenantappsv3.form',
            component: Form,
            props: true,
        },
        {
            path: 'view/:id?',
            name: 'tenantappsv3.view',
            component: Item,
            props: true,
        },
        {
            path: 'filters',
            name: 'tenantappsv3.filters',
            component: Filters,
            props: true,
        },

    ]
};

routes.push(routes_list);

export default routes;

