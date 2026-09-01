<template>
  <h3
    class="mt-1 flex cursor-pointer items-center text-left"
    @click.stop="selectCategory"
  >
    <Component
      :is="category.children.length > 0 ? 'span' : 'RouterLink'"
      :to="{ name: ProductPageList.PRODUCTS, query: { category: category.id } }"
      :class="[
        categoryConfig?.textClass,
        { 'inline-flex items-center': !!categoryConfig?.icon },
      ]"
      replace
    >
      <Component
        v-if="categoryConfig?.icon"
        :is="categoryConfig.icon"
        class="mr-2 h-4 w-4"
      />
      {{ category.name }}
    </Component>
    <Chevron2RightIconComponent
      v-if="category.children.length > 0"
      :class="{
        'ml-2 font-bold': !category.parentId,
        'mt-2 rotate-90 ease-in-out': showChildren,
      }"
      class="ml-2 fill-black stroke-black hover:text-secondary"
    />
  </h3>
  <div v-if="showChildren" class="ml-5">
    <MenuCategoryChildComponent
      v-for="cat in category.children"
      :key="cat.id"
      :category="cat"
    />
  </div>
</template>

<script lang="ts" setup>
import { PropType, ref, computed } from 'vue'

import router from '@/vuejs/router'

import { ProductPageList } from '@/vuejs/router/pages-list'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { Category } from '@/vuejs/types/Product/Category'
import {
  CATEGORY_CONFIGS,
  CategoryConfig,
  SUSTAINABLE_PURCHASES_CATEGORY_ID,
  SUSTAINABLE_PURCHASES_CATEGORY_NAME,
} from '@/vuejs/constants/categoryConfigs'

import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'

const props = defineProps({
  category: {
    required: true,
    type: Object as PropType<Category>,
  },
})

const emit = defineEmits(['selectCategory', 'closeMenu'])

const showChildren = ref<boolean>(false)

const categoryConfig = computed<CategoryConfig | undefined>(() => {
  if (CATEGORY_CONFIGS[props.category.id]) {
    return CATEGORY_CONFIGS[props.category.id]
  }

  // Fallback: certains channels peuvent exposer la catégorie avec un autre id.
  if (props.category.name === SUSTAINABLE_PURCHASES_CATEGORY_NAME) {
    return CATEGORY_CONFIGS[SUSTAINABLE_PURCHASES_CATEGORY_ID]
  }

  return undefined
})

const selectCategory = () => {
  if (props.category.children.length > 0) {
    emit('selectCategory', props.category)
  } else {
    sendGtmEvent('menu_click', {
      link_text: props.category.name,
      link_url: router.resolve({
        name: ProductPageList.PRODUCTS,
        query: { category: props.category.id },
      }).fullPath,
      origin_url: router.currentRoute.value.fullPath,
    })
    emit('closeMenu')
  }
}
</script>
