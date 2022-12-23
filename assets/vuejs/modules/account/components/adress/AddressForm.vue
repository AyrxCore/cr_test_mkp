<template>
  <form
      v-if="isEditedLoaded"
      @submit.prevent="onAddressFormSubmit"
  >
    <div class="grid grid-rows grid-flow-col gap-6">
      <div  class="mb-6">
          <LabelField title="Nom"/>
          <InputField
            v-model="currentAddress.name"
          />
      </div>
      <div  class="mb-6">
        <LabelField title="Entreprise"/>
        <InputField
            v-model="currentAddress.company"
        />
      </div>
    </div>
    <div class="grid grid-rows grid-flow-col gap-6">
      <div  class="mb-6">
        <LabelField title="Nom interlocuteur"/>
        <InputField
            v-model="currentAddress.last_name"
        />
        </div>
      <div  class="mb-6">
        <LabelField title="Prénom interlocuteur"/>
        <InputField
            v-model="currentAddress.first_name"
        />
        </div>
    </div>
    <div class="grid grid-rows grid-flow-col gap-12">
      <div  class="mb-6">
        <LabelField title="Adresse"/>
        <InputField
            v-model="currentAddress.street"
        />
      </div>
    </div>
    <div class="grid grid-rows grid-flow-col gap-6">
      <div  class="mb-6">
        <LabelField title="Code postal"/>
        <InputField
            v-model="currentAddress.postcode"
        />
      </div>
      <div  class="mb-6">
        <LabelField title="Ville"/>
        <InputField
            v-model="currentAddress.city"
        />
      </div>
    </div>
    <div class="grid grid-rows grid-flow-col gap-6">
      <div  class="mb-6">
        <LabelField title="Pays"/>
        <SelectField
            v-model="currentAddress.country"
            :options="countryStore.getCountriesForSelect()"
            placeholder="Sélectionner un pays"
        />
      </div>
      <div  class="mb-6">
        <LabelField title="Téléphone"/>
        <InputField
            v-model="currentAddress.phone"
        />
      </div>
    </div>
    <div class="flex justify-end">
      <ButtonComponent
          class="default-button mr-2 mb-2 flex items-center px-4 py-5 text-sm font-medium bg-transparent
             !text-purple-500 rounded-full border border-purple-600"
          :is-loading="isloading"
      >
        Enregistrer
      </ButtonComponent>
    </div>
  </form>
  <div
      v-else
      class="flex justify-center mt-5"
  >
    <LoaderSharedComponent
      class="text-purple-600"
    />
  </div>
</template>
<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import {useCompanyStore} from '@/vuejs/stores/company'
import {storeToRefs} from 'pinia'
import InputField from '@/vuejs/modules/shared/formfields/InputField.vue'
import LabelField from '@/vuejs/modules/shared/formfields/LabelField.vue'
import {onMounted, ref, watch} from 'vue'
import {useRoute} from 'vue-router'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import {useCoutryStore} from '@/vuejs/stores/country'
import SelectField from '@/vuejs/modules/shared/formfields/SelectField.vue'

const route = useRoute()
const companyStore = useCompanyStore()
const {currentAddress, isloading} = storeToRefs(companyStore)
const isEditing = ref<boolean>(false)
const isEditedLoaded = ref<boolean>(false)
const countryStore = useCoutryStore()
const {countries} = storeToRefs(countryStore)

onMounted(async () => {
  await countryStore.getCountries()
})

watch(
    () => route.params.id as number,
    async (id: number) => {
      if (!id) {
        companyStore.initNewAddress()
        isEditedLoaded.value = true
      } else if (id && companyStore.currentAddress === null) {
        isEditing.value = true
        await companyStore.getAddress(id)
        isEditedLoaded.value = true
      } else {
        isEditing.value = true
        isEditedLoaded.value = true
      }

    },
    {immediate: true}
)


const onAddressFormSubmit = async () => {
  companyStore.isloading = true
  if (isEditing.value) {
    await companyStore.updateAddress()
  } else {
    await companyStore.createAddress()
  }

  companyStore.isloading = false
}
</script>
