import { Component } from 'vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import FranceFlagIconComponent from '@/vuejs/modules/shared/icon/FranceFlagIconComponent.vue'

export interface ProductTagConfig {
  key: string
  icon: Component
  label: string
  borderColor: string
  bgColor: string
  textColor: string
  showOnCard: boolean
  showOnProduct: boolean
}

export const PRODUCT_TAGS_CONFIG: Record<string, ProductTagConfig> = {
  made_in_france: {
    key: 'made_in_france',
    icon: FranceFlagIconComponent,
    label: 'Fabriqué en France',
    borderColor: '#bfdbfe',
    bgColor: '#eff6ff',
    textColor: '#1e40af',
    showOnCard: true,
    showOnProduct: true,
  },
  achat_durable: {
    key: 'achat_durable',
    icon: LeafIconComponent,
    label: 'Achat durable',
    borderColor: '#bbf7d0',
    bgColor: '#f0fdf4',
    textColor: '#166534',
    showOnCard: true,
    showOnProduct: true,
  },
}
