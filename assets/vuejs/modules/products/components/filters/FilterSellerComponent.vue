<template>
  <div class="my-2 flex cursor-pointer items-center text-left text-sm">
    <input
      :id="`sellerRadio-${seller.id}`"
      v-model="sellerRadio"
      :checked="route.query.seller === seller.externalId"
      :value="seller.externalId"
      class="mr-3 cursor-pointer"
      name="sellerRadio"
      type="radio"
      @change="emit('change-seller')"
      @click="
        sendGtmEvent('select_filter', {
          filter_partner: seller.name,
          origin_url: router.currentRoute.value.fullPath,
        })
      "
    />
    <label :for="`sellerRadio-${seller.id}`" class="cursor-pointer">
      {{ seller.name }}
    </label>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'

import router from '@/vuejs/router'
import { sendGtmEvent } from '@/vuejs/services/gtm'

const route = useRoute()

const props = defineProps({
  seller: {
    required: true,
    type: Object,
  },
})

const sellerRadio = ref<boolean>()

const emit = defineEmits(['change-seller'])
</script>
