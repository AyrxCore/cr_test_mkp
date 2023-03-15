import { RouteRecordRaw } from 'vue-router'
import ProductsPage from '@/vuejs/modules/products/views/ProductsList.vue'
import CategoriesPage from '@/vuejs/modules/products/views/CategoriesContainer.vue'
import ProductPage from '@/vuejs/modules/products/views/Product.vue'
import AccordCadrePage from '@/vuejs/modules/products/views/AccordCadre.vue'

import { ProductPageList } from '@/vuejs/router/pages-list'

export const routes: RouteRecordRaw[] = [
  {
    path: `/app/${ ProductPageList.PRODUCTS }`,
    name: ProductPageList.PRODUCTS,
    component: ProductsPage,
    props: (route) => ({ query: route.query }),
  },
  {
    path: `/app/${ ProductPageList.CATEGORIES }`,
    name: ProductPageList.CATEGORIES,
    component: CategoriesPage,
  },
  {
    path: `/app/${ ProductPageList.PRODUCT }/:id`,
    component: ProductPage,
    name: ProductPageList.PRODUCT,
  },
  {
    path: `/app/${ProductPageList.ACCORD_CADRE}/:id`,
    name: ProductPageList.ACCORD_CADRE,
    component: AccordCadrePage,
  },
]
