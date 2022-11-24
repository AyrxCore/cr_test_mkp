import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

import {
  ProductPageList,
  routes as productsRoutes,
} from '@/vuejs/modules/products/routerProducts'

import Home from '@/vuejs/modules/home/views/HomePage.vue'
import { useUserStore } from '@/vuejs/stores/user'

export enum MainPageList {
  HOME_PAGE = 'home-page',
}

export const PageList = {
  ...MainPageList,
  ...ProductPageList,
}

const routes: RouteRecordRaw[] = [
  {
    path: '/app/home',
    name: PageList.HOME_PAGE,
    component: Home,
  },
  ...productsRoutes,
]

const router = createRouter({
  history: createWebHistory(),
  linkActiveClass: 'current-route',
  routes,
})

router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()

  if (!userStore.isLogged) {
    console.log('before get me', userStore.getToken)
    await userStore.getCurrentUserDatas()
    const host = window.location.protocol + '//' + window.location.host
    if (!userStore.isLogged) {
      window.location.href = host
    }
  }

  next()
})

router.beforeResolve(async (to) => {})

export default router
