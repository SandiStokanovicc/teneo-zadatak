import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        component: () => import("./Pages/Calendar.vue"),
    },

    {
        path: "/arhiva",
        component: () => import("./Pages/Archive.vue"),
    },

];

export default createRouter({
    history: createWebHistory(),
    routes,
});
