<template>
  <div class="py-12 px-6 lg:px-12">
    <h3 class="text-title-primary mb-5">
      Ces partenaires peuvent aussi vous intéresser
    </h3>
    <SellersCarouselComponent :params="params" :exclude-external-id="excludeExternalId" />
    <p class=" mt-12 text-xs text-gray-500">
      Les références, photographies, remises et tarifs des produits fournis
      sur la marketplace n'ont qu'une valeur indicative. Pour toute
      confirmation d'information, nous vous invitons à nous contacter.
    </p>
  </div>
</template>

<script lang="ts" setup>
import SellersCarouselComponent from '@/vuejs/modules/shared/SellersCarouselComponent.vue'

defineProps<{
  params?: object
  excludeExternalId?: string
}>()
</script>
