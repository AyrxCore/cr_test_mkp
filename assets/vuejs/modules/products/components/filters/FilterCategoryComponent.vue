<template>
  <div class="mt-1 flex items-center text-left text-sm text-primary">
    <input
      v-model="catRadio"
      name="catRadio"
      type="radio"
      :value="category.id"
      class="mr-1"
      :checked="category.checked"
      @change="handleCategorySelection(category)"
    />
    <label> {{ category.name }} ({{ category.productCount }}) </label>
    <Chevron2RightIconComponent
      v-if="category.children?.length > 0"
      :class="{
        'ml-2 h-3 w-6 font-bold': !category.parentId,
        'mr-2': category.parentId !== null,
        'mt-4 rotate-90 ease-in-out': showChildren,
      }"
      @click="toggleChildren"
    />
  </div>
  <div v-if="showChildren" class="ml-5">
    <FilterCategoryComponent
      v-for="cat in category.children"
      :key="cat.id"
      :category="cat"
      @change-category="handleCategorySelection(cat)"
    />
  </div>
</template>
<script lang="ts" setup>
import { PropType, ref } from 'vue'
import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'
import { Category } from '@/vuejs/types/Product/Category'

defineProps({
  category: {
    required: true,
    type: Object as PropType<Category>,
  },
  space: {
    type: Number,
    default: 0,
  },
})

const showChildren = ref<boolean>(false)
const catRadio = ref()

const emit = defineEmits(['change-category'])

const toggleChildren = () => {
  showChildren.value = !showChildren.value
}

const handleCategorySelection = async (category: Category) => {
  await emit('change-category', { category_id: category.id })
}
</script>

<style scoped></style>
