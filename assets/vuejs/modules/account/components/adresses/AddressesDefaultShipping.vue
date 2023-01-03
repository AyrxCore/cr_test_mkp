<template>
  <h3 class="mb-2 text-lg text-primary sm:text-[25px]">Votre adresse de livraison par défaut</h3>
  <div class="py-3 px-3 justify-between bg-white rounded-lg flex text-gray-500 mb-8 items-center text-base md:text-lg">
    <div>
       <span v-if="companyStore.getDefaultBillingAddress">
        {{ companyStore.getDefaultShippingAddress.company }},
        {{ companyStore.getDefaultShippingAddress.street }}
        {{ companyStore.getDefaultShippingAddress.postcode }}
        {{ companyStore.getDefaultShippingAddress.city }}
      </span>
      <span v-else>
        <LoaderSharedComponent
          class="text-purple-600"
        />
      </span>
    </div>
  </div>
</template>
<script lang="ts" setup>

import {useCompanyStore} from '@/vuejs/stores/company'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

const companyStore = useCompanyStore()
</script>
