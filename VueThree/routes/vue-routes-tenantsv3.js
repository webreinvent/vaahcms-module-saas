let routes= [];
let routes_list= [];

import List from '../pages/tenantsv3/List.vue'
import Form from '../pages/tenantsv3/Form.vue'
import Item from '../pages/tenantsv3/Item.vue'
import Filters from '../pages/tenantsv3/Filters.vue'

routes_list = {

    path: '/tenantsv3',
    name: 'tenantsv3.index',
    component: List,
    props: true,
    children:[
        {
            path: 'form/:id?',
            name: 'tenantsv3.form',
            component: Form,
            props: true,
        },
        {
            path: 'view/:id?',
            name: 'tenantsv3.view',
            component: Item,
            props: true,
        },
        {
            path: 'filters',
            name: 'tenantsv3.filters',
            component: Filters,
            props: true,
        },

    ]
};

routes.push(routes_list);

export default routes;

