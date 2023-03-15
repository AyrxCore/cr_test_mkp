<template>
    <div
      class="mt-1 items-center text-left text-primary flex text-sm"
    >
      <input
        v-model="catRadio"
        name="catRadio"
        type="radio"
        :value="category.id"
        class="mr-1"
        :checked="props.category.checked"
        @change="handleCategorySelection(props.category)"
      />
      <label>
        {{ props.category.name }} ({{ props.category.count }})
      </label>
      <Chevron2RightIconComponent
        v-if="props.category.child"
        :class="{
                'w-6 font-bold h-3 ml-2': props.category.parent === null,
                'mr-2': props.category.parent !== null,
                'mt-4 rotate-90 ease-in-out': showChildren
              }"
        @click="toggleChildren"
      />
    </div>
    <div
      v-if="showChildren"
      class="ml-5"
    >
      <FilterCategoryComponent
        v-for="cat in props.category.child"
        :key="cat.id"
        :category="cat"
        @change-category="handleCategorySelection(cat)"
      />
    </div>

</template>
<script lang="ts" setup>
import { ref } from 'vue'
import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'

const props = defineProps({
  category: {
    required: true,
    type: Object,
  },
  space: {
    type: Number,
    default: 0,
  }
})

const showChildren = ref<boolean>(false)
const catRadio = ref()

const emit = defineEmits(['change-category'])

const toggleChildren = (() =>  {
  showChildren.value = !showChildren.value
})

const handleCategorySelection = (async (category) =>  {
  if (category.child && category.child.length) {
    handleChildSelection(category)
  }
  handleParentSelection(category)
  await emit('change-category', { category_id: category.id})
})

const handleChildSelection = ((category) =>  {
  category.child.forEach((child) => {
    child.checked = category.checked
    handleChildSelection(child)
  })
})

const handleParentSelection = ((category) =>  {
  if (category.parent) {
    category.parent.checked = category.parent.child.every((child) => child.checked)
    handleParentSelection(category.parent)
    showChildren.value = !showChildren.value
  }
})
</script>

<style scoped></style>
