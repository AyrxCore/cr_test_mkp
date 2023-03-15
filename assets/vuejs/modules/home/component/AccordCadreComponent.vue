<template>
  <div class="flex h-full flex-col rounded-md bg-primary p-8">
    <div class="mx-auto">
      <img :src="properties.logo_partenaire" alt="Image produit" />
    </div>
    <p class="my-8 text-sm font-normal text-white sm:text-lg">
      Découvrez ou téléchargez les conditions négociées de ce partenaire
    </p>

    <RouterLink
      :to="{ path: `${ProductPageList.ACCORD_CADRE}/${accord.id}` }"
      class="button button-white button-white-primary flex justify-center"
    >
      <ArrowRightIconComponent />Découvrir l'accord cadre
    </RouterLink>
  </div>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { Product } from '@/vuejs/types/Product'

const props = defineProps({
  accord: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const properties = computed(() => {
  return props.accord.properties
})
</script>

<style scoped></style>
