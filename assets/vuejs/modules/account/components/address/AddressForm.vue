<template>
  <form v-if="isEditedLoaded" @submit.prevent="onAddressFormSubmit">
    <div class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-6">
      <div class="mb-6">
        <LabelField title="Nom de l'adresse *" />
        <InputField v-model="currentAddress.name" required />
      </div>
      <div class="mb-6">
        <LabelField title="Entreprise *" />
        <InputField v-model="currentAddress.company" required />
      </div>
    </div>
    <div class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-6">
      <div class="mb-6">
        <LabelField title="Nom interlocuteur" />
        <InputField v-model="currentAddress.lastName" />
      </div>
      <div class="mb-6">
        <LabelField title="Prénom interlocuteur" />
        <InputField v-model="currentAddress.firstName" />
      </div>
    </div>
    <div class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-12">
      <div class="mb-6">
        <LabelField title="Adresse *" />
        <InputField v-model="currentAddress.street" required />
      </div>
    </div>
    <div class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-6">
      <div class="mb-6">
        <LabelField title="Code postal *" />
        <InputField
          v-model="currentAddress.postcode"
          required
          pattern="(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$"
        />
      </div>
      <div class="mb-6">
        <LabelField title="Ville *" />
        <InputField v-model="currentAddress.city" required />
      </div>
    </div>
    <div
      class="md:grid-rows flex grid-cols-2 flex-col border-none md:grid md:grid-flow-col md:gap-6"
    >
      <div class="mb-6 w-full">
        <LabelField title="Pays *" />
        <SelectField
          v-model="currentAddress.country"
          :options="countryStore.getCountriesForSelect()"
          placeholder="Sélectionner un pays"
          required
        />
      </div>
      <div class="mb-6">
        <LabelField title="Téléphone" />
        <InputField
          v-model="currentAddress.phone"
          pattern="^((\+)33|0)[1-9](\d{2}){4}$"
        />
      </div>
    </div>
    <div class="flex justify-between md:justify-end">
      <ButtonComponent
        class="button-primary-outline mr-2"
        type="button"
        @click="onCancelClick"
      >
        Annuler
      </ButtonComponent>
      <ButtonComponent
        class="button-primary"
        :is-loading="isLoading"
        @click="emit('submitAddress')"
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
import { onMounted, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import InputField from '@/vuejs/modules/shared/formfields/InputField.vue'
import LabelField from '@/vuejs/modules/shared/formfields/LabelField.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import SelectField from '@/vuejs/modules/shared/formfields/SelectField.vue'

import router, { PageList } from '@/vuejs/router'
import { useAddressStore } from '@/vuejs/stores/address'
import { useRoute } from 'vue-router'
import { useCountryStore } from '@/vuejs/stores/country'

const route = useRoute()
const addressStore = useAddressStore()
const { currentAddress, isLoading } = storeToRefs(addressStore)
const isEditing = ref<boolean>(false)
const isEditedLoaded = ref<boolean>(false)
const countryStore = useCountryStore()

const props = defineProps({
  type: {
    required: false,
    type: String,
  },
})

const emit = defineEmits(['submitAddress', 'cancelCreateAddress'])

onMounted(async () => {
  await countryStore.getCountries()
})

watch(
  () => route.params.id as number,
  async (id: number) => {
    if (!id) {
      addressStore.initNewAddress(props.type)
      isEditedLoaded.value = true
    } else if (id && addressStore.currentAddress === null) {
      isEditing.value = true
      await addressStore.getAddress(id)
      isEditedLoaded.value = true
    } else {
      isEditing.value = true
      isEditedLoaded.value = true
    }
  },
  { immediate: true },
)

const onAddressFormSubmit = async () => {
  addressStore.isLoading = true
  if (isEditing.value) {
    await addressStore.updateAddress()
  } else {
    await addressStore.createAddress()
  }

  addressStore.isLoading = false
}

const onCancelClick = () => {
  emit('cancelCreateAddress')
  router.push({
    name: PageList.ADDRESSES,
  })
}
</script>
