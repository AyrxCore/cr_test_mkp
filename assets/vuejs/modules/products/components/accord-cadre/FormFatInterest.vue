<template>
  <FormContactInterest
    :form-data="formData"
    :gtm-event="gtmEvent"
    product-label="Accord-cadre concerné"
    :show-message="true"
    :message-required="true"
    message-placeholder="Veuillez préciser votre besoin véhicule au partenaire"
    :close-on-success="true"
    @success="emit('success')"
  />
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'

import { Product } from '@/vuejs/types/Product'
import FormContactInterest from '@/vuejs/modules/products/components/FormContactInterest.vue'

const props = defineProps({
  accord: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const emit = defineEmits(['success'])

const formData = computed(() => ({
  accordId: props.accord.properties['accord-id'],
  accordName: props.accord.name,
  productName: props.accord.name,
  partnerName: props.accord.seller?.name || '',
}))

const gtmEvent = computed(() => ({
  eventName: 'fat_form_submission',
  eventData: {
    form_type: 'fat_preciser_besoin_vehicule',
    accord_name: props.accord.name,
    partner: props.accord.seller?.name,
  },
}))
</script>

