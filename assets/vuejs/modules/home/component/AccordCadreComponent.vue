<template>
  <div class="flex h-full flex-col rounded-md bg-primary p-8">
    <div class="mx-auto">
      <img :src="properties.logo_partenaire" alt="Image produit" />
    </div>
    <p
      class="my-8 text-center text-sm font-normal uppercase text-white sm:text-lg"
    >
      {{ accord.name }}
    </p>

    <RouterLink
      :to="{ name: ProductPageList.ACCORD_CADRE, params: { id: accord.id } }"
      class="button button-white button-white-primary flex justify-center hover:!text-white"
    >
      <ArrowRightIconComponent />
      Découvrir l'accord-cadre
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
