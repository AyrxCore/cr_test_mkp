<template>
  <div class="my-3 text-left text-sm">
    <div class="flex items-center">
      <input
        v-model="catRadio"
        :checked="category.checked"
        :value="category.id"
        :id="`categoryRadio-${category.id}`"
        name="catRadio"
        type="radio"
        class="mr-3 cursor-pointer"
        @change="handleCategorySelection(category)"
      />
      <label :for="`categoryRadio-${category.id}`" class="cursor-pointer">
        {{ category.name }} ({{ category.productCount }})
      </label>
      <Chevron2RightIconComponent
        v-if="category.children?.length > 0"
        :class="{
          'ml-4 h-3 w-6 font-bold': !category.parentId,
          'mr-1': category.parentId !== null,
          'rotate-90 ease-in-out': showChildren,
        }"
        @click="toggleChildren"
      />
    </div>
    <div v-if="showChildren" class="ml-4 mt-2">
      <FilterCategoryComponent
        v-for="cat in category.children"
        :key="cat.id"
        :category="cat"
        @change-category="handleCategorySelection(cat)"
      />
    </div>
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
