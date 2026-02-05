<template>
  <FormContactInterest
    :form-data="formData"
    :gtm-event="gtmEvent"
    product-label="Produit/service concerné"
    :show-message="product?.notSellableFormWithMessage"
    :message-required="false"
    message-placeholder="Veuillez préciser votre besoin au partenaire (optionnel)"
  />
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'

import { Product } from '@/vuejs/types/Product'
import FormContactInterest from '@/vuejs/modules/products/components/FormContactInterest.vue'

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const formData = computed(() => ({
  accordId: props.product.properties['accord-id'],
  accordName: props.product.properties['accord-name'],
  productName: props.product.name,
  partnerName: props.product.seller?.name || '',
}))

const gtmEvent = computed(() => ({
  eventName: 'contact_form_submission',
  eventData: {
    form_type: 'not_sellable_product',
  },
}))
</script>
