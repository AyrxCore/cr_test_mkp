import { RouteRecordRaw } from 'vue-router'
import ProductsPage from '@/vuejs/modules/products/views/ProductsList.vue'
import CategoriesPage from '@/vuejs/modules/products/views/CategoriesList.vue'
import ProductPage from '@/vuejs/modules/products/views/Product.vue'
import AccordCadrePage from '@/vuejs/modules/products/views/AccordCadre.vue'

export enum ProductPageList {
  PRODUCTS = 'produits',
  CATEGORIES = 'categories',
  PRODUCT = 'produit',
  ACCORD_CADRE = 'accord-cadre',
}

export const routes: RouteRecordRaw[] = [
  {
    path: '/app/produits',
    name: ProductPageList.PRODUCTS,
    component: ProductsPage,
    props: route => ({ query: route.query })
  },
  {
    path: '/app/categories',
    name: ProductPageList.CATEGORIES,
    component: CategoriesPage,
  },
  {
    path: '/app/produit/:id',
    component: ProductPage,
    name: ProductPageList.PRODUCT,
  },
  {
    path: `/app/${ ProductPageList.ACCORD_CADRE }/:id`,
    name: ProductPageList.ACCORD_CADRE,
    component: AccordCadrePage,
  },
]
