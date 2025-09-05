import { type Component } from 'vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'

export const SUSTAINABLE_PURCHASES_CATEGORY_ID = 954

export type CategoryConfig = {
  icon?: Component
  textClass?: string
}

export const CATEGORY_CONFIGS: Record<number, CategoryConfig> = {
  [SUSTAINABLE_PURCHASES_CATEGORY_ID]: {
    icon: LeafIconComponent,
    textClass: 'text-green-qantis',
  },
}
