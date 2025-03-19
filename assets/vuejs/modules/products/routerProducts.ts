import { RouteRecordRaw } from 'vue-router'

const ProductsPage = () =>
  import('@/vuejs/modules/products/views/ProductsList.vue')
import CategoriesPage from '@/vuejs/modules/products/views/CategoriesContainer.vue'
import ProductPage from '@/vuejs/modules/products/views/Product.vue'
import AccordCadrePage from '@/vuejs/modules/products/views/AccordCadre.vue'
import SellersPage from '@/vuejs/modules/products/views/SellersList.vue'

import { ProductPageList } from '@/vuejs/router/pages-list'

export const routes: RouteRecordRaw[] = [
  {
    path: '/products',
    name: ProductPageList.PRODUCTS,
    component: ProductsPage,
    props: (route) => ({ query: route.query }),
  },
  {
    path: '/categories',
    name: ProductPageList.CATEGORIES,
    component: CategoriesPage,
  },
  {
    path: '/products/:slug',
    component: ProductPage,
    name: ProductPageList.PRODUCT,
  },
  {
    path: '/accord-cadres/:slug',
    name: ProductPageList.ACCORD_CADRE,
    component: AccordCadrePage,
  },
  {
    path: '/sellers',
    name: ProductPageList.SELLERS,
    component: SellersPage,
    props: (route) => ({ query: route.query }),
  },
]
