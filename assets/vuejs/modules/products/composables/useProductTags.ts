import { computed, Ref } from 'vue'
import { PRODUCT_TAGS_CONFIG, ProductTagConfig } from '@/vuejs/modules/products/config/productTags'

type TagContext = 'card' | 'product'

export function useProductTags(tags: Ref<string[] | undefined>, context: TagContext) {
  const visibleTags = computed((): ProductTagConfig[] =>
    (tags.value ?? [])
      .filter((key) => {
        const config = PRODUCT_TAGS_CONFIG[key]
        if (!config) return false
        return context === 'card' ? config.showOnCard : config.showOnProduct
      })
      .map((key) => PRODUCT_TAGS_CONFIG[key] as ProductTagConfig)
  )

  return { visibleTags }
}
