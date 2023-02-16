<template>
  <form v-if="isEditedLoaded" @submit.prevent="onAddressFormSubmit">
    <div class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-6">
      <div class="mb-6">
        <LabelField title="Nom *" />
        <InputField v-model="currentAddress.name" required="true" />
      </div>
      <div class="mb-6">
        <LabelField title="Entreprise *" />
        <InputField v-model="currentAddress.company" required="true" />
      </div>
    </div>
    <div class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-6">
      <div class="mb-6">
        <LabelField title="Nom interlocuteur" />
        <InputField v-model="currentAddress.last_name" />
      </div>
      <div class="mb-6">
        <LabelField title="Prénom interlocuteur" />
        <InputField v-model="currentAddress.first_name" />
      </div>
    </div>
    <div class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-12">
      <div class="mb-6">
        <LabelField title="Adresse *" />
        <InputField v-model="currentAddress.street" required="true" />
      </div>
    </div>
    <div class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-6">
      <div class="mb-6">
        <LabelField title="Code postal *" />
        <InputField
          v-model="currentAddress.postcode"
          required="true"
          pattern="(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$"
        />
      </div>
      <div class="mb-6">
        <LabelField title="Ville *" />
        <InputField v-model="currentAddress.city" required="true" />
      </div>
    </div>
    <div class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-6">
      <div class="mb-6">
        <LabelField title="Pays *" />
        <SelectField
          v-model="currentAddress.country"
          :options="countryStore.getCountriesForSelect()"
          placeholder="Sélectionner un pays"
          required="true"
        />
      </div>
      <div class="mb-6">
        <LabelField title="Téléphone" />
        <InputField v-model="currentAddress.phone" />
      </div>
    </div>
    <div class="flex justify-between md:justify-end">
      <ButtonComponent
        class="default-button mr-2 mb-2 flex items-center rounded-full border border-purple-600 bg-transparent
        px-4 py-5 text-sm font-medium !text-purple-500 hover:!text-white"
        type="button"
        @click="onCancelClick"
      >
        Annuler
      </ButtonComponent>
      <ButtonComponent
        class="default-button mr-2 mb-2 flex items-center rounded-full border border-purple-600 bg-transparent
        px-4 py-5 text-sm font-medium !text-purple-500 hover:!text-white"
        :is-loading="isloading"
      >
        Enregistrer
      </ButtonComponent>
    </div>
  </form>
  <div v-else class="mt-5 flex justify-center">
    <LoaderSharedComponent class="text-purple-600" />
  </div>
</template>
<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { useCompanyStore } from '@/vuejs/stores/company'
import { storeToRefs } from 'pinia'
import InputField from '@/vuejs/modules/shared/formfields/InputField.vue'
import LabelField from '@/vuejs/modules/shared/formfields/LabelField.vue'
import { onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { useCoutryStore } from '@/vuejs/stores/country'
import SelectField from '@/vuejs/modules/shared/formfields/SelectField.vue'
import router, { PageList } from '@/vuejs/router'

const route = useRoute()
const companyStore = useCompanyStore()
const { currentAddress, isloading } = storeToRefs(companyStore)
const isEditing = ref<boolean>(false)
const isEditedLoaded = ref<boolean>(false)
const countryStore = useCoutryStore()
const { countries } = storeToRefs(countryStore)

const props = defineProps({
  type: {
    required: false,
    type: String,
  },
})

onMounted(async () => {
  await countryStore.getCountries()
})

watch(
  () => route.params.id as number,
  async (id: number) => {
    if (!id) {
      companyStore.initNewAddress(props.type)
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
  { immediate: true },
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

const onCancelClick = () => {
  router.push({
    name: PageList.ADDRESSES,
  })
}
</script>
