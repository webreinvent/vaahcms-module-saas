let routes= [];

import dashboard from "./vue-routes-dashboard";
import TenantApp from "./vue-routes-tenantapp";

routes = routes.concat(dashboard);
routes = routes.concat(TenantApp);

export default routes;
