import ServersList from "./../pages/servers/List";
import ServersCreate from "../pages/servers/Create";
import ServersView from "./../pages/servers/View";
import ServersEdit from "./../pages/servers/Edit";

import GetAssets from './middleware/GetAssets'
import LayoutBackend from "./../layouts/Backend";

let routes_servers=[];

let list =     {
    path: '/servers',
    component: LayoutBackend,
    props: true,
    meta: {
        middleware: [
            GetAssets
        ]
    },
    children: [
        {
            path: '/',
            name: 'servers.list',
            component: ServersList,
            props: true,
            meta: {
                middleware: [
                    GetAssets
                ]
            },
            children: [
                {
                    path: 'create',
                    name: 'servers.create',
                    component: ServersCreate,
                    props: true,
                    meta: {
                        middleware: [
                            GetAssets
                        ]
                    },
                },
                {
                    path: 'view/:id',
                    name: 'servers.view',
                    component: ServersView,
                    props: true,
                    meta: {
                        middleware: [
                            GetAssets
                        ]
                    },
                },
                {
                    path: 'edit/:id',
                    name: 'servers.edit',
                    component: ServersEdit,
                    props: true,
                    meta: {
                        middleware: [
                            GetAssets
                        ]
                    },
                }

            ]
        }

    ]
};


routes_servers.push(list);

export default routes_servers;