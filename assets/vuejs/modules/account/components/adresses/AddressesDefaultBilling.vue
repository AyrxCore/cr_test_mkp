<template>
  <h3 class="primary mb-2 text-[25px]">Votre adresse de facturation par défaut</h3>
  <div class="py-3 px-3 justify-between bg-white rounded-lg flex text-gray-500 mb-8 items-center">
    <div>
      <span v-if="companyStore.getDefaultBillingAddress">
        {{ companyStore.getDefaultBillingAddress.company }},
        {{ companyStore.getDefaultBillingAddress.street }}
        {{ companyStore.getDefaultBillingAddress.postcode }}
        {{ companyStore.getDefaultBillingAddress.city }}
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
