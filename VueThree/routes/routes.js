let routes= [];

import dashboard from "./vue-routes-dashboard";
import serverv3 from "./vue-routes-serversv3";

routes = routes.concat(serverv3);
routes = routes.concat(dashboard);

export default routes;
