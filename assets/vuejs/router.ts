import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import Cookies from 'js-cookie'

import Home from '@/vuejs/modules/home/views/HomePage.vue'
import Contact from '@/vuejs/modules/contact/views/ContactPage.vue'
import NotFoundPage from '@/vuejs/modules/PageNotFound.vue'
import LegalDocument from '@/vuejs/modules/LegalDocument.vue'
import MapPage from '@/vuejs/modules/map/views/MapPage.vue'

import { routes as productsRoutes } from '@/vuejs/modules/products/routerProducts'
import { routes as actualitesRoutes } from '@/vuejs/modules/actualites/routerActualites'
import { routes as cartRoutes } from '@/vuejs/modules/cart/routerCart'
import { routes as accountRoutes } from '@/vuejs/modules/account/routerAccount'

import { useCartStore } from '@/vuejs/stores/cart'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useUserStore } from '@/vuejs/stores/user'

import {
  AccountPageList,
  CartPageList,
  MainPageList,
  NewsPageList,
  ProductPageList,
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
    component: LegalDocument,
    props: {
      title: 'Mentions légales',
      page: PageList.MENTIONS_LEGALES_PAGE,
    },
  },
  {
    path: '/politique-de-confidentialite',
    name: PageList.POLITIQUE_DE_CONFIDENTIALITE,
    component: LegalDocument,
    props: {
      title: 'Politique de confidentialité',
      page: PageList.POLITIQUE_DE_CONFIDENTIALITE,
    },
  },
  {
    path: '/conditions-generales-d-utilisation',
    name: PageList.CGU_PAGE,
    component: LegalDocument,
    props: {
      title: "Conditions générales d'utilisation",
      page: PageList.CGU_PAGE,
    },
  },
  {
    path: '/page-not-found',
    name: PageList.PAGE_NOT_FOUND,
    component: NotFoundPage,
  },
  {
    path: '/map',
    name: PageList.PARTNERS_MAP,
    component: MapPage,
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
  scrollBehavior(to) {
    if (to.hash) {
      return { el: to.hash }
    }
    window.scrollTo(0, 0)
  },
})

router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()
  const cartStore = useCartStore()
  const channelStore = useChannelStore()

  const isAdyenRedirectReturn = ['redirectResult', 'MD', 'cres'].some(
    (param) => param in to.query,
  )

  if (!userStore.isLogged) {
    await userStore.getCurrentUserData()
    if (!userStore.isLogged) {
      Cookies.remove('BEARER')
      Cookies.remove('PHPSESSID')
      Cookies.remove('neoAutoLogin')
      location.reload()
    }

    if (
      !isAdyenRedirectReturn &&
      [
        CartPageList.CART_ADDRESSES,
        CartPageList.CART_SHIPMENTS,
        CartPageList.CART_PAYMENT,
        CartPageList.CART_PAYMENT_SEPA,
      ].includes(to.name) &&
      !cartStore.hasAllTermsChecked
    ) {
      router.push({ name: CartPageList.CART_RECAP })
    }

    const optionnalPages = [
      PageList.FAVORITES_LIST,
      PageList.FAVORITES_DETAILS,
      PageList.SAVED_CARTS,
      PageList.SAVED_CARTS_DETAILS,
    ]
    if (optionnalPages.includes(to.name)) {
      const option = optionnalPages.find((x) => channelStore.isAllowedToShow(x))
      if (option === undefined) {
        router.push({ name: PageList.PAGE_NOT_FOUND })
      }
    }
  }

  next()
})

export default router
