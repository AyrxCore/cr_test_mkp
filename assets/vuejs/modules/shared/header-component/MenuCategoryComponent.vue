<template>
  <div
    v-if="modelValue"
    v-click-outside="closeMenu"
    class="fixed top-0 left-0 z-50 h-screen w-full bg-white px-5 py-2.5 text-sm
    text-primary shadow sm:absolute sm:h-fit sm:w-auto sm:rounded"
  >
    <div class="flex items-center">
      <router-link
        :to="{name: ProductPageList.CATEGORIES}"
        class="font-bold hover:bg-gray-200">
        Voir toutes les catégories
      </router-link>
      <CloseIcon
        class="ml-auto cursor-pointer hover:text-secondary"
        @click.stop="closeMenu"
      />
    </div>
    <hr class="my-2.5" />
    <div
      v-for="category in categories"
      :key="category.id"
      class="w-[100%] items-center py-1 !text-sm"
    >
      <CategoryComponent
        :category="category"
        class="!text-sm"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import CloseIcon from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'
import { ProductPageList } from '@/vuejs/router/pages-list';
import { useCategoryStore } from '@/vuejs/stores/category'
import CategoryComponent from '@/vuejs/modules/products/components/CategoryComponent.vue'

const emit = defineEmits(['update:modelValue'])

const props = defineProps({
  modelValue: {
    required: true,
    type: Boolean,
  },
})


const categoryStore = useCategoryStore()

const categories = computed(() => {
  return categoryStore.categories
})
const closeMenu = (): void => {
  emit('update:modelValue', false)
}
</script>
