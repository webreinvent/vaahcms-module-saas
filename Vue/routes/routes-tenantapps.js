import TenantAppsList from "./../pages/tenantapps/List";
import TenantAppsCreate from "../pages/tenantapps/Create";
import TenantAppsView from "./../pages/tenantapps/View";
import TenantAppsEdit from "./../pages/tenantapps/Edit";

import GetAssets from './middleware/GetAssets'
import LayoutBackend from "./../layouts/Backend";

let routes_tenantapps=[];

let list =     {
    path: '/tenantapps',
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
            name: 'tenantapps.list',
            component: TenantAppsList,
            props: true,
            meta: {
                middleware: [
                    GetAssets
                ]
            },
            children: [
                {
                    path: 'create',
                    name: 'tenantapps.create',
                    component: TenantAppsCreate,
                    props: true,
                    meta: {
                        middleware: [
                            GetAssets
                        ]
                    },
                },
                {
                    path: 'view/:id',
                    name: 'tenantapps.view',
                    component: TenantAppsView,
                    props: true,
                    meta: {
                        middleware: [
                            GetAssets
                        ]
                    },
                },
                {
                    path: 'edit/:id',
                    name: 'tenantapps.edit',
                    component: TenantAppsEdit,
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


routes_tenantapps.push(list);

export default routes_tenantapps;