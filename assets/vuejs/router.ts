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

import {
  AccountPageList,
  routes as accountRoutes,
} from '@/vuejs/modules/account/routerAccount'

import {
  PartnersPageList,
  routes as partnerRoutes,
} from '@/vuejs/modules/partners/routerPartners'

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
  ...AccountPageList,
  ...PartnersPageList,
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
  ...accountRoutes,
  ...partnerRoutes,
]

const router = createRouter({
  history: createWebHistory(),
  linkActiveClass: 'current-route',
  routes,
  scrollBehavior() {
    window.scrollTo(0, 0)
  },
})

router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()

  if (!userStore.isLogged) {
    await userStore.getCurrentUserDatas()
    const host = window.location.protocol + '//' + window.location.host
    if (!userStore.isLogged) {
      window.location.href = host
    }
  }

  next()
})

// router.beforeResolve(async (to) => {})

export default router
