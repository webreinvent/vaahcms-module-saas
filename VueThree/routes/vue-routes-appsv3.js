let routes= [];
let routes_list= [];

import List from '../pages/appsv3/List.vue'
import Form from '../pages/appsv3/Form.vue'
import Item from '../pages/appsv3/Item.vue'
import Filters from '../pages/appsv3/Filters.vue'

routes_list = {

    path: '/appsv3',
    name: 'appsv3.index',
    component: List,
    props: true,
    children:[
        {
            path: 'form/:id?',
            name: 'appsv3.form',
            component: Form,
            props: true,
        },
        {
            path: 'view/:id?',
            name: 'appsv3.view',
            component: Item,
            props: true,
        },
        {
            path: 'filters',
            name: 'appsv3.filters',
            component: Filters,
            props: true,
        },

    ]
};

routes.push(routes_list);

export default routes;

