<template>
  <h3 class="mb-2 mt-8 text-lg text-primary">
    Paiement par prélèvement bancaire
  </h3>
  <div class="flex flex-col-reverse lg:grid lg:grid-cols-4 lg:gap-4 lg:px-0">
    <div class="col-span-3 mt-2 flex flex-col lg:mt-0">
      <LoadingComponent v-if="isMandatesLoading" />
      <template
        v-else-if="hasCompanyMandates && !newMandate && !isMandatesLoading"
      >
        <h3 class="mb-4 text-primary">Mandat(s) sauvegardé(s)</h3>
        <LoadingComponent v-if="isSEPALoading" />
        <div
          v-else
          v-for="mandate in companyMandates"
          :key="mandate.id"
          class="mb-4 flex w-full items-center justify-between bg-white px-4 py-2"
        >
          <span class="text-gray-700">{{ mandate.iban }}</span>
          <ButtonComponent
            class="button-primary"
            type="button"
            @click="selectSEPA({ mandateId: mandate.id })"
          >
            Utiliser et payer
          </ButtonComponent>
        </div>
      </template>
      <template v-else-if="newMandate">
        <form class="lg:w-1/2" @submit.prevent="confirmForm">
          <div class="mb-6">
            <LabelField class="text-primary" title="IBAN" />
            <InputField v-model="iban" required />
          </div>
          <div class="mb-6">
            <LabelField class="text-primary" title="BIC" />
            <InputField v-model="bic" required />
          </div>
          <div class="mb-6">
            <LabelField class="text-primary" title="Nom du bénéficiaire" />
            <InputField v-model="ownerName" required />
          </div>
          <div class="mb-4">
            <LabelField class="text-primary" title="N° de téléphone portable" />
            <InputField
              v-model="phone"
              type="tel"
              pattern="\+33[6-7][0-9]{8}"
              placeholder="+33"
              required
            />
          </div>
          <div
            v-if="Array.isArray(errors) && errors.length > 0"
            class="my-4 text-red-500"
          >
            <ul>
              <li v-for="error in errors">
                {{ error }}
              </li>
            </ul>
          </div>
          <ButtonComponent
            v-if="hasCompanyMandates"
            class="button-primary-outline mr-4"
            type="button"
            @click="newMandate = false"
          >
            Retour
          </ButtonComponent>
          <ButtonComponent class="button-primary" :is-loading="isSEPALoading">
            Enregistrer et payer
          </ButtonComponent>
        </form>
      </template>
      <ButtonComponent
        class="button-primary-outline w-fit"
        v-if="!newMandate && !isMandatesLoading && !isSEPALoading"
        @click="newMandate = true"
      >
        Saisir un nouveau mandat
      </ButtonComponent>
    </div>
    <CartRightSideComponent :has-payment-methods="false">
      <template #title>Récapitulatif</template>
    </CartRightSideComponent>
  </div>
</template>
<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { isValidIBAN, isValidBIC } from 'ibantools/jsnext/ibantools'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import InputField from '@/vuejs/modules/shared/formfields/InputField.vue'
import LabelField from '@/vuejs/modules/shared/formfields/LabelField.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'

import { useCartStore } from '@/vuejs/stores/cart'
import { SepaData } from '@/vuejs/types/Cart'

const cartStore = useCartStore()

const { cart, companyMandates, hasCompanyMandates, selectedSepa } =
  storeToRefs(cartStore)

const newMandate = ref<boolean>(false)
const isMandatesLoading = ref<boolean>(false)
const isSEPALoading = ref<boolean>(false)
const errors = ref<string[]>()
const iban = ref<string>('')
const bic = ref<string>('')
const ownerName = ref<string>('')
const phone = ref<string>('')

onMounted(async (): Promise<void> => {
  isMandatesLoading.value = true
  await cartStore.getCompanyMandates()
  isMandatesLoading.value = false
})

const confirmForm = async () => {
  errors.value = []
  if (!isValidIBAN(iban.value)) {
    errors.value = ["Le format de l'IBAN est invalide"]
    return
  }
  if (!isValidBIC(bic.value)) {
    errors.value = ['Le format du BIC est invalide']
    return
  }

  await selectSEPA({
    iban: iban.value,
    bic: bic.value.toUpperCase(),
    ownerName: ownerName.value,
    phone: phone.value,
  })
}

const selectSEPA = async (sepaData: SepaData) => {
  isSEPALoading.value = true
  try {
    await cartStore.updateCartPaymentMethod(selectedSepa.value.type)
    const result = await cartStore.updateCartPaymentSepaInfos(sepaData)
    if (result?.signing_url) {
      window.location.replace(result?.signing_url)
    } else if (result && result.signing_url === null) {
      window.location.replace(
        `${window.location.origin}/api/buyer/cart/${cart.value.id}/confirm`,
      )
    } else {
      window.location.replace(`${window.location.origin}/cart/payment-error`)
    }
  } catch (err) {
    errors.value = err
    isSEPALoading.value = false
  }
}
</script>

<style scoped></style>
