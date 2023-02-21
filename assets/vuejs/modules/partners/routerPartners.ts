import { RouteRecordRaw } from 'vue-router'
import AccordCadrePage from '@/vuejs/modules/partners/views/AccordCadre.vue'

export enum PartnersPageList {
  ACCORD_CADRE = 'accord-cadre',
}

export const routes: RouteRecordRaw[] = [
  {
    path: `/app/${ PartnersPageList.ACCORD_CADRE }/:id`,
    name: PartnersPageList.ACCORD_CADRE,
    component: AccordCadrePage,
  },
]
