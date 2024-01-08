import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

import Home from '@/vuejs/modules/home/views/HomePage.vue'
import Contact from '@/vuejs/modules/contact/views/ContactPage.vue'
import NotFoundPage from '@/vuejs/modules/PageNotFound.vue'
import LegalDocument from '@/vuejs/modules/LegalDocument.vue'

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

import {
  CGU_PAGE_ID,
  MENTIONS_LEGALES_PAGE_ID,
  POLITIQUE_DE_CONFIDENTIALITE_PAGE_ID,
} from '@/vuejs/services/const'

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
      pageId: MENTIONS_LEGALES_PAGE_ID,
    },
  },
  {
    path: '/politique-de-confidentialite',
    name: PageList.POLITIQUE_DE_CONFIDENTIALITE,
    component: LegalDocument,
    props: {
      title: 'Politique de confidentialité',
      pageId: POLITIQUE_DE_CONFIDENTIALITE_PAGE_ID,
    },
  },
  {
    path: '/conditions-generales-d-utilisation',
    name: PageList.CGU_PAGE,
    component: LegalDocument,
    props: {
      /* eslint-disable-next-line quotes */
      title: "Conditions générales d'utilisation",
      pageId: CGU_PAGE_ID,
    },
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

  if (!userStore.isLogged) {
    await userStore.getCurrentUserData()
    if (!userStore.isLogged) {
      document.cookie = 'BEARER=; Max-Age=0'
      document.cookie = 'PHPSESSID=; Max-Age=0'
      location.reload()
    }

    if (
      [
        CartPageList.CART_ADDRESSES,
        CartPageList.CART_SHIPMENTS,
        CartPageList.CART_PAYMENT,
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

// router.beforeResolve(async (to) => {})

export default router
