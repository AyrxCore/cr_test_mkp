import { type Component } from 'vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'

export const SUSTAINABLE_PURCHASES_CATEGORY_ID = '0013400008'
export const SUSTAINABLE_PURCHASES_CATEGORY_NAME = 'Achats responsables'

export type CategoryConfig = {
  icon?: Component
  textClass?: string
}

export const CATEGORY_CONFIGS: Record<string, CategoryConfig> = {
  [SUSTAINABLE_PURCHASES_CATEGORY_ID]: {
    icon: LeafIconComponent,
    textClass: 'text-green-qantis',
  },
}
