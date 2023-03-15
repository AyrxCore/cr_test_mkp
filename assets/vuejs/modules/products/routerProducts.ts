import { RouteRecordRaw } from 'vue-router'
import ProductsPage from '@/vuejs/modules/products/views/ProductsList.vue'
import CategoriesPage from '@/vuejs/modules/products/views/CategoriesList.vue'
import ProductPage from '@/vuejs/modules/products/views/Product.vue'
import AccordCadrePage from '@/vuejs/modules/products/views/AccordCadre.vue'

import { ProductPageList } from '@/vuejs/router/pages-list'

export const routes: RouteRecordRaw[] = [
  {
    path: '/app/products',
    name: ProductPageList.PRODUCTS,
    component: ProductsPage,
    props: (route) => ({ query: route.query }),
  },
  {
    path: '/app/categories',
    name: ProductPageList.CATEGORIES,
    component: CategoriesPage,
  },
  {
    path: '/app/product/:id',
    component: ProductPage,
    name: ProductPageList.PRODUCT,
  },
  {
    path: `/app/${ProductPageList.ACCORD_CADRE}/:id`,
    name: ProductPageList.ACCORD_CADRE,
    component: AccordCadrePage,
  },
]
