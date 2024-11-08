let routes= [];

import dashboard from "./vue-routes-dashboard";
import AppsV3 from "./vue-routes-appsv3";
import serverv3 from "./vue-routes-serversv3";

routes = routes.concat(serverv3);
routes = routes.concat(dashboard);
routes = routes.concat(AppsV3);

export default routes;
