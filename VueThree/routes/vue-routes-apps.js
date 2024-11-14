let routes= [];
let routes_list= [];

import List from '../pages/apps/List.vue'
import Form from '../pages/apps/Form.vue'
import Item from '../pages/apps/Item.vue'
import Filters from '../pages/apps/Filters.vue'

routes_list = {

    path: '/apps',
    name: 'apps.index',
    component: List,
    props: true,
    children:[
        {
            path: 'form/:id?',
            name: 'apps.form',
            component: Form,
            props: true,
        },
        {
            path: 'view/:id?',
            name: 'apps.view',
            component: Item,
            props: true,
        },
        {
            path: 'filters',
            name: 'apps.filters',
            component: Filters,
            props: true,
        },

    ]
};

routes.push(routes_list);

export default routes;

