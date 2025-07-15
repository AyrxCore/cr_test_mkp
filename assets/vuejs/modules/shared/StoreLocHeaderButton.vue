<template>
  <RouterLink
    :to="{ name: MainPageList.PARTNERS_MAP }"
    class="mr-2 flex text-primary"
  >
    <MarkerIconComponent class="mr-1" size="24" />
    <span class="hidden underline xl:flex">Carte des partenaires</span>
  </RouterLink>
</template>

<script lang="ts" setup>
import MarkerIconComponent from '@/vuejs/modules/shared/icon/MarkerIconComponent.vue'
import { MainPageList } from '@/vuejs/router/pages-list'
</script>
