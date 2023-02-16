<template>
  <div class="flex justify-between">
    <div class="inline-flex bg-white  rounded-md w-full">
      <label class="text-gray-500 flex-none">Recherche:</label>
      <input v-model="searchQuery" type="text"  name="query" class="rounded-md border border-gray-300 ml-2 w-3/6 h-[27px]"/>
    </div>
    <div class="text-white px-2 py-1 w-fit flex">
      <DefaultButtonComponent
          :btn-text-color="'text-gray-500'"
          :btn-color="'bg-transparent'"
          :rounded="'rounded-md'"
          class="border border-gray-600 text-gray-500 py-2"
      >
        Précédent
      </DefaultButtonComponent>
      <div class="text-gray-500 px-4 py-2 border items-center border-gray-300 rounded-md h-[32px] mr-2">
        <span>1</span>
      </div>
      <DefaultButtonComponent
          :btn-text-color="'text-gray-500'"
          :btn-color="'bg-transparent'"
          :rounded="'rounded-md'"
          class="border border-gray-600 text-gray-500 py-2"
      >
        Suivant
      </DefaultButtonComponent>
    </div>
  </div>
</template>
<script lang="ts" setup>
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'
</script>
