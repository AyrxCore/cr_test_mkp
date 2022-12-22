<template>
  <h3 class="primary mb-2 text-[25px]">Votre adresse de livraison préférée</h3>
  <span class="text-gray-500 text-lg">Votre adresse de livraison par défaut est : : </span>
  <div class="py-3 px-3 justify-between bg-white rounded-lg flex text-gray-500 mb-8 items-center">
    <div>
      <span>QANTIS, 185 allée des Cyprès, 69760 LIMONEST, FRANCE</span>
    </div>
    <div class="float-right text-white px-2 py-1 w-fit">
      <button><EditIconComponent /></button>
    </div>
  </div>
</template>
<script lang="ts" setup>
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
</script>
