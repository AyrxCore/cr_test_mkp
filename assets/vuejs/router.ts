import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

import {
  ProductPageList,
  routes as productsRoutes,
} from '@/vuejs/modules/products/routerProducts'

import {
  ActualitesPageList,
  routes as actualitesRoutes,
} from '@/vuejs/modules/actualites/routerActualites'

import {
  CartPageList,
  routes as cartRoutes,
} from '@/vuejs/modules/cart/routerCart'

import Home from '@/vuejs/modules/home/views/HomePage.vue'
import Contact from '@/vuejs/modules/ContactPage.vue'
import { useUserStore } from '@/vuejs/stores/user'

export enum MainPageList {
  HOME_PAGE = 'home-page',
  CONTACT_PAGE = 'contact-page',
}

export const PageList = {
  ...MainPageList,
  ...ProductPageList,
  ...ActualitesPageList,
  ...CartPageList,
}

const routes: RouteRecordRaw[] = [
  {
    path: '/app/home',
    name: PageList.HOME_PAGE,
    component: Home,
  },
  {
    path: '/app/contact',
    name: PageList.CONTACT_PAGE,
    component: Contact,
  },
  ...productsRoutes,
  ...actualitesRoutes,
  ...cartRoutes,
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
      //window.location.href = host
    }
  }

  next()
})

router.beforeResolve(async (to) => {})

export default router
