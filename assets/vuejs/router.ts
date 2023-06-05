import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

import { routes as productsRoutes } from '@/vuejs/modules/products/routerProducts'

import { routes as actualitesRoutes } from '@/vuejs/modules/actualites/routerActualites'

import { routes as cartRoutes } from '@/vuejs/modules/cart/routerCart'

import { routes as accountRoutes } from '@/vuejs/modules/account/routerAccount'

import Home from '@/vuejs/modules/home/views/HomePage.vue'
import Contact from '@/vuejs/modules/contact/views/ContactPage.vue'
import MentionsLegales from '@/vuejs/modules/MentionsLegales.vue'
import PolitiqueDeConfidentialite from '@/vuejs/modules/PolitiqueDeConfidentialite.vue'
import CGU from '@/vuejs/modules/CGU.vue'
import NotFoundPage from '@/vuejs/modules/PageNotFound.vue'
import { useUserStore } from '@/vuejs/stores/user'
import { useCartStore } from '@/vuejs/stores/cart'

import {
  MainPageList,
  ProductPageList,
  NewsPageList,
  CartPageList,
  AccountPageList,
} from '@/vuejs/router/pages-list'

export const PageList = {
  ...MainPageList,
  ...ProductPageList,
  ...NewsPageList,
  ...CartPageList,
  ...AccountPageList,
}

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    redirect: { name: PageList.HOME_PAGE },
  },
  {
    path: '/home',
    name: PageList.HOME_PAGE,
    component: Home,
  },
  {
    path: '/contact',
    name: PageList.CONTACT_PAGE,
    component: Contact,
  },
  {
    path: '/mentions-legales',
    name: PageList.MENTIONS_LEGALES_PAGE,
    component: MentionsLegales,
  },
  {
    path: '/politique-de-confidentialite',
    name: PageList.POLITIQUE_DE_CONFIDENTIALITE,
    component: PolitiqueDeConfidentialite,
  },
  {
    path: '/conditions-generales-d-utilisations',
    name: PageList.CGU_PAGE,
    component: CGU,
  },
  {
    path: '/page-not-found',
    name: PageList.PAGE_NOT_FOUND,
    component: NotFoundPage,
  },
  ...productsRoutes,
  ...actualitesRoutes,
  ...cartRoutes,
  ...accountRoutes,
  {
    path: '/:pathMatch(.*)*',
    redirect: { name: `${PageList.PAGE_NOT_FOUND}` },
  },
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
  const cartStore = useCartStore()

  if (!userStore.isLogged) {
    await userStore.getCurrentUserDatas()
    if (!userStore.isLogged) {
      location.reload()
    }

    if (
      [CartPageList.ADDRESSES, CartPageList.PAYMENT].includes(to.name) &&
      !cartStore.hasAllTermsChecked
    ) {
      router.push({ name: CartPageList.RECAP })
    }
  }

  next()
})

// router.beforeResolve(async (to) => {})

export default router
