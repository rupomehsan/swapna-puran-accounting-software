import setup from ".";
import Form from "../pages/Form.vue";
import Details from "../pages/Details.vue";
import Layout from "../pages/Layout.vue";

let route_prefix = setup.route_prefix;
let route_path = setup.route_path;

const routes = {
    path: route_path,
    component: Layout,
    children: [
        {
            // /configuration/all shows the settings form (singleton)
            path: "all",
            name: "All" + route_prefix,
            component: Form,
        },
        {
            path: "details/:id",
            name: "Details" + route_prefix,
            component: Details,
        },
        {
            path: "edit/:id",
            name: "Edit" + route_prefix,
            component: Form,
        },
    ],
};

export default routes;
