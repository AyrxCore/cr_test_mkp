<template>
  <DropdownListComponent
    class="lg:overflow-y-auto lg:h-1/2 c-scrollbar !rounded-l-md"
  >
    <template #button-label> Filtres </template>
    <template #content>
      <div
        v-for="(filter, index) in filters.properties"
        :key="index"
        class="h-max bg-white px-7.5 pt-7.5 pb-2 text-lg"
      >
        <h3 class="text-sm md:text-base lg:text-lg text-primary">{{ filter.name}} ({{ filter.count }})</h3>

        <select
          v-if="filter.type === 'choice'"
          class="w-full flex"
        >
          <option
            v-for="child in filter.child"
            :key="child.id"
            :value="child.value">
            {{ child.name }}
          </option>
        </select>
      </div>
    </template>
  </DropdownListComponent>
</template>
<script lang="ts" setup>
import DropdownListComponent from '@/vuejs/modules/shared/DropdownListComponent.vue'
import { computed } from 'vue';

const props = defineProps({
  filters: {
    required: true,
    type: Object,
  },
})

</script>

<style scoped>
.c-scrollbar::-webkit-scrollbar {
  width: 12px;
}

.c-scrollbar::-webkit-scrollbar-track {
  @apply bg-white;
}

.c-scrollbar::-webkit-scrollbar-thumb {
  @apply bg-primary;
}

.c-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #9f9f9f;
}
</style>
