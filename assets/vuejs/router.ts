import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

import Home from '@/vuejs/modules/home/views/HomePage.vue'

export enum MainPageList {
  HOME_PAGE = 'home-page',
}

export const PageList = {
  ...MainPageList,
}

const routes: RouteRecordRaw[] = [
  {
    path: '/app',
    name: PageList.HOME_PAGE,
    component: Home,
  },
]

const router = createRouter({
  history: createWebHistory(),
  linkActiveClass: 'current-route',
  routes,
})

export default router
