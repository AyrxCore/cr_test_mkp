import { RouteRecordRaw } from 'vue-router'
import ActualitesPage from '@/vuejs/modules/actualites/views/ActualitesList.vue'
import ActualitePage from '@/vuejs/modules/actualites/views/Actualite.vue'

export enum ActualitesPageList {
  ACTUALITES = 'actualites',
  ACTUALITE = 'actualite',
}

export const routes: RouteRecordRaw[] = [
  {
    path: '/app/' + ActualitesPageList.ACTUALITES,
    name: ActualitesPageList.ACTUALITES,
    component: ActualitesPage,
  },
  {
    path: '/app/' +ActualitesPageList.ACTUALITE+'/:slug',
    component: ActualitePage,
    name: ActualitesPageList.ACTUALITE,
  },
]
