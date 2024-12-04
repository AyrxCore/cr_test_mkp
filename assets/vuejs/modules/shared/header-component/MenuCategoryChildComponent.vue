<template>
  <h3
    class="mt-1 flex cursor-pointer items-center text-left"
    @click.capture.stop="selectCategory"
  >
    <component
      :is="category.children.length > 0 ? 'span' : 'RouterLink'"
      :to="{ name: ProductPageList.PRODUCTS, query: { category: category.id } }"
      replace
    >
      {{ category.name }}
    </component>
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
import { PropType, ref } from 'vue'
import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { Category } from '@/vuejs/types/Product/Category'

const props = defineProps({
  category: {
    required: true,
    type: Object as PropType<Category>,
  },
})

const emit = defineEmits(['selectCategory', 'closeMenu'])

const showChildren = ref<boolean>(false)

const selectCategory = () => {
  if (props.category.children.length > 0) {
    emit('selectCategory', props.category)
  } else {
    emit('closeMenu')
  }
}
</script>

<style scoped></style>
