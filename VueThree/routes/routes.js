let routes= [];

import dashboard from "./vue-routes-dashboard";
import AppsV3 from "./vue-routes-appsv3";
import TenantV3 from "./vue-routes-tenantsv3";

routes = routes.concat(dashboard);
routes = routes.concat(TenantV3);
routes = routes.concat(dashboard);
routes = routes.concat(AppsV3);


export default routes;
