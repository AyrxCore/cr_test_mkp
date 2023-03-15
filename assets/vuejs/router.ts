import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

import { routes as productsRoutes } from '@/vuejs/modules/products/routerProducts'

import { routes as actualitesRoutes } from '@/vuejs/modules/actualites/routerActualites'

import { routes as cartRoutes } from '@/vuejs/modules/cart/routerCart'

import { routes as accountRoutes } from '@/vuejs/modules/account/routerAccount'

import Home from '@/vuejs/modules/home/views/HomePage.vue'
import Contact from '@/vuejs/modules/contact/views/ContactPage.vue'
import NotFoundPage from '@/vuejs/modules/PageNotFound.vue'
import { useUserStore } from '@/vuejs/stores/user'
import { useCartStore } from '@/vuejs/stores/cart'

export enum MainPageList {
  HOME_PAGE = 'home',
  CONTACT_PAGE = 'contact',
  PAGE_NOT_FOUND = 'page-non-trouvee',
}

import {
  MainPageList,
  ProductPageList,
  ActualitesPageList,
  CartPageList,
  AccountPageList,
} from '@/vuejs/router/pages-list'

export const PageList = {
  ...MainPageList,
  ...ProductPageList,
  ...ActualitesPageList,
  ...CartPageList,
  ...AccountPageList,
}

const routes: RouteRecordRaw[] = [
  {
    path: `/app/${PageList.HOME_PAGE}`,
    name: PageList.HOME_PAGE,
    component: Home,
  },
  {
    path: `/app/${PageList.CONTACT_PAGE}`,
    name: PageList.CONTACT_PAGE,
    component: Contact,
  },
  {
    path: `/app/${PageList.PAGE_NOT_FOUND}`,
    name: PageList.PAGE_NOT_FOUND,
    component: NotFoundPage,
  },
  {
    path: '/app/:pathMatch(.*)*',
    redirect: `/app/${PageList.PAGE_NOT_FOUND}`,
  },
  ...productsRoutes,
  ...actualitesRoutes,
  ...cartRoutes,
  ...accountRoutes,
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
    const host = window.location.protocol + '//' + window.location.host
    if (!userStore.isLogged) {
      window.location.href = host
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
