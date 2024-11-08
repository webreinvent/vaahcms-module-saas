let routes= [];

import dashboard from "./vue-routes-dashboard";
import TenantV3 from "./vue-routes-tenantsv3";

routes = routes.concat(dashboard);
routes = routes.concat(TenantV3);

export default routes;
