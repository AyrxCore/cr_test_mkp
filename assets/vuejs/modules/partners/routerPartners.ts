import { RouteRecordRaw } from 'vue-router'
import AccordCadrePage from '@/vuejs/modules/partners/views/AccordCadre.vue'
import ProductsPartnerPage from '@/vuejs/modules/partners/views/ProductsPartner.vue'

export enum PartnersPageList {
  ACCORD_CADRE = 'accord-cadre',
  PARTNER = 'partner',
  LISTE_PRODUITS_PARTENAIRE = 'liste-produits-partenaire',
}

export const routes: RouteRecordRaw[] = [
  {
    path: `/app/${ PartnersPageList.ACCORD_CADRE }/:id`,
    name: PartnersPageList.ACCORD_CADRE,
    component: AccordCadrePage,
  },
  {
    path: `/app/${PartnersPageList.LISTE_PRODUITS_PARTENAIRE}/:id`,
    component: ProductsPartnerPage,
    name: PartnersPageList.LISTE_PRODUITS_PARTENAIRE,
  },
]
