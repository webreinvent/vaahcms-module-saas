import TenantsList from "./../pages/tenants/List";
import TenantsCreate from "../pages/tenants/Create";
import TenantsView from "./../pages/tenants/View";
import TenantsEdit from "./../pages/tenants/Edit";

import GetAssets from './middleware/GetAssets'
import LayoutBackend from "./../layouts/Backend";

let routes_tenants=[];

let list =     {
    path: '/',
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
            name: 'tenants.list',
            component: TenantsList,
            props: true,
            meta: {
                middleware: [
                    GetAssets
                ]
            },
            children: [
                {
                    path: 'create',
                    name: 'tenants.create',
                    component: TenantsCreate,
                    props: true,
                    meta: {
                        middleware: [
                            GetAssets
                        ]
                    },
                },
                {
                    path: 'view/:id',
                    name: 'tenants.view',
                    component: TenantsView,
                    props: true,
                    meta: {
                        middleware: [
                            GetAssets
                        ]
                    },
                },
                {
                    path: 'edit/:id',
                    name: 'tenants.edit',
                    component: TenantsEdit,
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


routes_tenants.push(list);

export default routes_tenants;
