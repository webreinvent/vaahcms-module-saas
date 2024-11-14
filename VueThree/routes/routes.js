let routes= [];

import dashboard from "./vue-routes-dashboard";
import Apps from "./vue-routes-apps";
import Tenant from "./vue-routes-tenants";
import server from "./vue-routes-servers";
import TenantApps from "./vue-routes-tenantapps";

routes = routes.concat(server);
routes = routes.concat(TenantApps);
routes = routes.concat(dashboard);
routes = routes.concat(Tenant);
routes = routes.concat(dashboard);
routes = routes.concat(Apps);


export default routes;
