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
        {{ category.name }}
      </label>
      <Chevron2RightIconComponent
        v-if="category.children?.length > 0"
        :class="{
          'ml-4 h-3 w-6 font-bold': !category.parentId,
          'mr-1': category.parentId !== null,
          'rotate-90 ease-in-out': showChildren,
        }"
        class="cursor-pointer"
        @click="toggleChildren"
      />
    </div>
    <div v-show="showChildren" class="ml-4 mt-2">
      <FilterCategoryComponent
        v-for="cat in category.children"
        :key="cat.id"
        :category="cat"
        @change-category="handleCategorySelection"
        @open-hierarchy="openHierarchy"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { PropType, onMounted, ref } from 'vue'
import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'
import { Category } from '@/vuejs/types/Product/Category'

const props = defineProps({
  category: {
    required: true,
    type: Object as PropType<Category>,
  },
})

const emit = defineEmits(['change-category', 'open-hierarchy'])

const showChildren = ref<boolean>(false)

const toggleChildren = (): void => {
  showChildren.value = !showChildren.value
}

const handleCategorySelection = (categoryId: number): void => {
  emit('change-category', categoryId)
}

const openHierarchy = (): void => {
  toggleChildren()
  emit('open-hierarchy')
}

onMounted((): void => {
  if (props.category.checked) {
    emit('open-hierarchy')
  }
})
</script>
