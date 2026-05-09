import setup from ".";
import All from "../pages/All.vue";
import Form from "../pages/Form.vue";
import Details from "../pages/Details.vue";
import Layout from "../pages/Layout.vue";

const routes = {
    path: setup.route_path,
    component: Layout,
    children: [
        { path: "all",         name: "All"     + setup.route_prefix, component: All     },
        { path: "create",      name: "Create"  + setup.route_prefix, component: Form    },
        { path: "details/:id", name: "Details" + setup.route_prefix, component: Details },
        { path: "edit/:id",    name: "Edit"    + setup.route_prefix, component: Form    },
    ],
};

export default routes;
