<template>
  <div
    v-if="modelValue"
    v-click-outside="closeMenu"
    class="flex absolute left-0 z-10 w-full bg-white px-5 py-2.5 text-sm
    text-primary shadow flex-wrap h-[80vh] overflow-auto sm:w-auto sm:rounded !lg:h-auto c-scrollbar"
  >
    <div class="flex items-center">
      <router-link
        :to="{name: ProductPageList.CATEGORIES}"
        class="font-bold hover:bg-gray-200 uppercase">
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
      <MenuCategoryChildComponent
        :category="category"
        class="!text-sm"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import CloseIcon from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { useCategoryStore } from '@/vuejs/stores/category'
import MenuCategoryChildComponent from '@/vuejs/modules/shared/header-component/MenuCategoryChildComponent.vue'

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

<style scoped>
.c-scrollbar::-webkit-scrollbar {
  width: 12px;
}

.c-scrollbar::-webkit-scrollbar-track {
  @apply bg-white;
}

.c-scrollbar::-webkit-scrollbar-thumb {
  @apply bg-primary;
}

.c-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #9f9f9f;
}
</style>
