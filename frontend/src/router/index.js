//import vue router
import { createRouter, createWebHistory } from 'vue-router'

//define a routes
const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import( /* webpackChunkName: "Auth" */ "@/views/home/Index"),
        // component: () => import( /* webpackChunkName: "Auth" */ "@/views/auth/Index"),
    },
    {
        path: '/login',
        name: 'login',
        component: () => import( /* webpackChunkName: "Auth" */ "@/views/auth/Index"),
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import( /* webpackChunkName: "Auth" */ "@/views/dashboard/Index"),
    },
    {
        path: '/school',
        name: 'school',
        component: () => import( /* webpackChunkName: "Auth" */ "@/views/school/Index"),
    },
]

//create router
const router = createRouter({
    history: createWebHistory(),
    routes  // config routes
})

export default router
