<template>
  <h3
    class="mt-6 flex items-center text-left text-primary"
    :class="{
      'text-sm font-bold md:text-base lg:text-lg': !category.parentId,
      'flex justify-between': category.parentId !== null,
    }"
  >
    <RouterLink
      :to="{
        name: ProductPageList.PRODUCTS,
        query: { category: category.id },
      }"
    >
      {{ category.name }}
    </RouterLink>
    <Chevron2RightIconComponent
      v-if="category.children.length > 0"
      :class="{
        'ml-2 h-5 w-10 font-bold': !category.parentId,
        'mr-2': category.parentId !== null,
        'mt-4 rotate-90 ease-in-out': showChildren,
      }"
      @click="toggleChildren"
    />
  </h3>
  <div v-if="showChildren" class="ml-5">
    <CategoryComponent
      v-for="categoryChild in category.children"
      :key="categoryChild.id"
      :category="categoryChild"
    />
  </div>
</template>
<script lang="ts" setup>
import { PropType, ref } from 'vue'
import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { Category } from '@/vuejs/types/Product/Category'

defineProps({
  category: {
    required: true,
    type: Object as PropType<Category>,
  },
})

const showChildren = ref<boolean>(false)

const toggleChildren = () => {
  showChildren.value = !showChildren.value
}
</script>

<style scoped></style>
