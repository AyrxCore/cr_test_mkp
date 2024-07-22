<template>
  <div class="my-3 text-left text-sm">
    <div class="flex items-center">
      <input
        :id="`categoryRadio-${category.id}`"
        :checked="category.checked"
        :value="category.id"
        type="radio"
        class="mr-3 cursor-pointer"
        @change="handleCategorySelection(category.id)"
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
        @change-category="handleCategorySelection"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { PropType, ref, watch } from 'vue'
import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'
import { Category } from '@/vuejs/types/Product/Category'

const props = defineProps({
  category: {
    required: true,
    type: Object as PropType<Category>,
  },
  space: {
    type: Number,
    default: 0,
  },
})

const emit = defineEmits(['change-category'])

const showChildren = ref<boolean>(false)

const toggleChildren = () => {
  showChildren.value = !showChildren.value
}

const handleCategorySelection = async (categoryId: number) => {
  await emit('change-category', categoryId)
}

const isAnyChildChecked = (category) => {
  if (category.checked) {
    return true
  }
  if (category.children) {
    return category.children.some(isAnyChildChecked)
  }
  return false
}

watch(
  () => props.category,
  (newVal) => {
    if (isAnyChildChecked(newVal) && !newVal.checked) {
      showChildren.value = true
    }
    return false
  },
  { deep: true, immediate: true }
)
</script>
