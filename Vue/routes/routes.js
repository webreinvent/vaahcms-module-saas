let routes=[];
let routes_list=[];


//----------Middleware
import GetAssets from './middleware/GetAssets'
//----------Middleware


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

import Backend from './../layouts/Backend'
import Index from './../pages/dashboard/Index'

routes_list =     {
    path: '/',
    component: Backend,
    props: true,
    meta: {
        middleware: [
            GetAssets
        ]
    },
    children: [
        {
            path: '/',
            name: 'cms.index',
            component: Index,
            props: true,
            meta: {
                middleware: [
                    GetAssets
                ]
            },
        },

    ]
};

routes.push(routes_list);

/*
|--------------------------------------------------------------------------
| Content Types Routes
|--------------------------------------------------------------------------
*/


export default routes;
