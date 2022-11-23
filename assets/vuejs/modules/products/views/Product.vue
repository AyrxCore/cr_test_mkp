<template>
  <BaseTemplate title="Qantis - MarketPlace">
    Page de produit
    <CarouselListSharedComponent />
    <LoaderSharedComponent class="mt-4" />
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
</script>

<style scoped></style>
