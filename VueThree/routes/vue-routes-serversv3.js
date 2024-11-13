let routes= [];
let routes_list= [];

import List from '../pages/serversv3/List.vue'
import Form from '../pages/serversv3/Form.vue'
import Item from '../pages/serversv3/Item.vue'
import Filters from '../pages/serversv3/Filters.vue'

routes_list = {

    path: '/serversv3',
    name: 'serversv3.index',
    component: List,
    props: true,
    children:[
        {
            path: 'form/:id?',
            name: 'serversv3.form',
            component: Form,
            props: true,
        },
        {
            path: 'view/:id?',
            name: 'serversv3.view',
            component: Item,
            props: true,
        },
        {
            path: 'filters',
            name: 'serversv3.filters',
            component: Filters,
            props: true,
        },

    ]
};

routes.push(routes_list);

export default routes;

