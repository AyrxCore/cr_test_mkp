<template>
  <div class="my-2 cursor-pointer text-sm">
    <label :for="property.id" class="cursor-pointer">
      {{ property.name }}
    </label>
    <select
      :id="property.id"
      v-model="selected"
      class="flex w-full cursor-pointer"
      @change="
        emit('change-property', { property_id: property.id, value: selected })
      "
    >
      <option v-for="value in property.values" :key="value" :value="value">
        {{ value }}
      </option>
    </select>
  </div>
</template>

<script lang="ts" setup>
import { PropType, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

import { Property } from '@/vuejs/types/Product/Property'

const route = useRoute()

const props = defineProps({
  property: {
    required: true,
    type: Object as PropType<Property>,
  },
})

const emit = defineEmits(['change-property'])

const selected = ref<string>('')

onMounted((): void => {
  const query = route.query
  if (query.property_id === props.property.id) {
    selected.value = route.query.value as string
  }
})
</script>
