import { RouteRecordRaw } from 'vue-router'
import ProductsPage from '@/vuejs/modules/products/views/ProductsList.vue'
import CategoriesPage from '@/vuejs/modules/products/views/CategoriesContainer.vue'
import ProductPage from '@/vuejs/modules/products/views/Product.vue'
import AccordCadrePage from '@/vuejs/modules/products/views/AccordCadre.vue'

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
    path: '/products/:id',
    component: ProductPage,
    name: ProductPageList.PRODUCT,
  },
  {
    path: '/accord-cadres/:id',
    name: ProductPageList.ACCORD_CADRE,
    component: AccordCadrePage,
  },
]
