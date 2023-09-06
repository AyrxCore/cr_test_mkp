<template>
  <h3 class="mt-1 flex items-center text-left text-primary">
    <RouterLink
      :to="{ name: ProductPageList.PRODUCTS, query: { category: category.id } }"
      replace
    >
      {{ category.name }}
    </RouterLink>
    <Chevron2RightIconComponent
      v-if="category.children.length > 0"
      class="ml-2"
      :class="{
        'ml-2 font-bold': !category.parentId,
        'mt-2 rotate-90 ease-in-out': showChildren,
      }"
      @click="toggleChildren"
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
