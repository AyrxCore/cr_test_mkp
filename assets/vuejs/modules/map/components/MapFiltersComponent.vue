<template>
  <div class="w-full rounded-lg bg-white pt-2 md:p-7">
    <h4 class="mb-4 text-lg font-bold text-primary">Catégories</h4>

    <div class="space-y-2">
      <div
        v-for="category in categories"
        :key="category.id"
        class="flex items-center"
      >
        <input
          :id="`category-${category.id}`"
          :checked="
            selectedCategory === category.id ||
            (selectedCategory === null && category.id === 'all')
          "
          class="h-4 w-4 text-primary focus:ring-primary"
          name="category"
          type="radio"
          @change="
            handleCategoryChange(category.id === 'all' ? null : category.id)
          "
          @click="
            sendGtmEvent('partner_locator_filter', {
              filter_category: category.name,
            })
          "
        />
        <label
          :for="`category-${category.id}`"
          class="ml-2 cursor-pointer text-sm font-medium text-gray-700"
        >
          {{ category.name }}
        </label>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { PropType } from 'vue'

import { sendGtmEvent } from '@/vuejs/services/gtm'

const props = defineProps({
  categories: {
    type: Array as PropType<Array<{ id: string; name: string }>>,
    required: true,
  },
  selectedCategory: {
    type: String,
    required: false,
    default: null,
  },
})

const emit = defineEmits(['category-changed'])

const handleCategoryChange = (categoryId: string | null) => {
  emit('category-changed', categoryId)
}
</script>
