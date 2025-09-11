<template>
  <div class="my-2 flex cursor-pointer items-center text-left text-sm">
    <input
      :id="`companyRadio-${company.id}`"
      v-model="companyRadio"
      :checked="company.checked"
      :value="company.id"
      class="mr-3 cursor-pointer"
      name="companyRadio"
      type="radio"
      @change="handleCompanySelection(company)"
      @click="
        sendGtmEvent('select_filter', {
          filter_partner: company.name,
          origin_url: router.currentRoute.value.fullPath,
        })
      "
    />
    <label :for="`companyRadio-${company.id}`" class="cursor-pointer">
      {{ company.name }}
    </label>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import router from '@/vuejs/router'
import { sendGtmEvent } from '@/vuejs/services/gtm'

const props = defineProps({
  company: {
    required: true,
    type: Object,
  },
})

const companyRadio = ref()

const emit = defineEmits(['change-company'])

const handleCompanySelection = async (company) => {
  await emit('change-company', { company_id: company.id })
}
</script>
