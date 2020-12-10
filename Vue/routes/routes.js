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

import routes_tenants  from './routes-tenants'
import routes_tenantapps  from './routes-tenantapps'
import routes_server  from './routes-servers'
import routes_apps  from './routes-apps'


routes = routes.concat(routes, routes_tenants, routes_server, routes_apps, routes_tenantapps);

/*
|--------------------------------------------------------------------------
| Content Types Routes
|--------------------------------------------------------------------------
*/


export default routes;
