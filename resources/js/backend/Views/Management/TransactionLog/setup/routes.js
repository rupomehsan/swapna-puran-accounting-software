import setup from ".";
import All from "../pages/All.vue";
import Details from "../pages/Details.vue";
import Layout from "../pages/Layout.vue";

const routes = {
    path: setup.route_path,
    component: Layout,
    children: [
        { path: "all",         name: "All"     + setup.route_prefix, component: All     },
        { path: "details/:id", name: "Details" + setup.route_prefix, component: Details },
    ],
};

export default routes;
