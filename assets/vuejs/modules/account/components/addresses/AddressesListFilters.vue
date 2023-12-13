<template>
  <div class="flex justify-between">
    <div class="inline-flex w-full rounded-md bg-white">
      <label class="flex-none text-gray-500">Recherche:</label>
      <input
        v-model="searchQuery"
        type="text"
        name="query"
        class="ml-2 h-[27px] w-3/6 rounded-md border border-gray-300"
      />
    </div>
    <div class="flex w-fit px-2 py-1 text-white">
      <DefaultButtonComponent
        :btn-text-color="''"
        :btn-color="'bg-transparent'"
        :rounded="'rounded-md'"
        class="border border-gray-600 py-2 text-gray-500"
      >
        Précédent
      </DefaultButtonComponent>
      <div
        class="mr-2 h-[32px] items-center rounded-md border border-gray-300 px-4 py-2 text-gray-500"
      >
        <span>1</span>
      </div>
      <DefaultButtonComponent
        :btn-text-color="'text-gray-500'"
        :btn-color="'bg-transparent'"
        :rounded="'rounded-md'"
        class="border border-gray-600 py-2 text-gray-500"
      >
        Suivant
      </DefaultButtonComponent>
    </div>
  </div>
</template>
<script lang="ts" setup>
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'
</script>
