let routes= [];
let routes_list= [];

import List from '../pages/servers/List.vue'
import Form from '../pages/servers/Form.vue'
import Item from '../pages/servers/Item.vue'
import Filters from '../pages/servers/Filters.vue'

routes_list = {

    path: '/servers',
    name: 'servers.index',
    component: List,
    props: true,
    children:[
        {
            path: 'form/:id?',
            name: 'servers.form',
            component: Form,
            props: true,
        },
        {
            path: 'view/:id?',
            name: 'servers.view',
            component: Item,
            props: true,
        },
        {
            path: 'filters',
            name: 'servers.filters',
            component: Filters,
            props: true,
        },

    ]
};

routes.push(routes_list);

export default routes;

