import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import {
  LoginPageList,
  routes as loginRoutes,
} from '@/vuejs/modules/login/routerLogin'

import {
  ProductPageList,
  routes as productsRoutes,
} from '@/vuejs/modules/products/routerProducts'

import {
  ActualitesPageList,
  routes as actualitesRoutes,
} from '@/vuejs/modules/actualites/routerActualites'

import Home from '@/vuejs/modules/home/views/HomePage.vue'
import { useUserStore } from '@/vuejs/stores/user'

export enum MainPageList {
  HOME_PAGE = 'home-page',
}

export const PageList = {
  ...MainPageList,
  ...LoginPageList,
  ...ProductPageList,
  ...ActualitesPageList,
}

const routes: RouteRecordRaw[] = [
  {
    path: '/app/home',
    name: PageList.HOME_PAGE,
    component: Home,
  },
  ...loginRoutes,
  ...productsRoutes,
  ...actualitesRoutes,
]

const router = createRouter({
  history: createWebHistory(),
  linkActiveClass: 'current-route',
  routes,
})

router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()
  // if (to.name !== PageList.LOGIN_AUTH) {
  //   if (!userStore.isLogged) {
  //     next({ name: PageList.LOGIN_AUTH })
  //   }
  //   if (userStore.user === null && userStore.getToken !== null) {
  //     console.log('before get me', userStore.getToken)
  //     await userStore.getCurrentUserDatas()
  //   }
  // }
  next()
})

router.beforeResolve(async (to) => {})

export default router
