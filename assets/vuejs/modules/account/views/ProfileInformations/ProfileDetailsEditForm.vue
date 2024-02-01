<template>
  <AccountPage>
    <template #right-side>
      <h3 class="text-title-primary mb-4 mt-2 xl:mt-0">
        Modifier mes coordonnées
      </h3>
      <form @submit.prevent="onDetailsFormSubmit">
        <div class="grid-rows grid grid-flow-col gap-6">
          <div class="mb-6">
            <LabelField title="Nom" />
            <InputField
              v-model="userStore.editingInfo.lastName"
              type="text"
              required
            />
          </div>
        </div>
        <div class="grid-rows grid grid-flow-col gap-6">
          <div class="mb-6">
            <LabelField title="Prénom" />
            <InputField
              v-model="userStore.editingInfo.firstName"
              type="text"
              required
            />
          </div>
        </div>
        <div class="grid-rows grid grid-flow-col gap-6">
          <div class="mb-6">
            <LabelField title="Téléphone fixe" />
            <InputField
              v-model="userStore.editingInfo.phone"
              type="text"
              pattern="^((\+)33|0)[1-9](\d{2}){4}$"
              title="Ex: 0478123456"
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
          <ButtonComponent class="button-primary" :is-loading="isLoading">
            Enregistrer
          </ButtonComponent>
        </div>
      </form>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import { onBeforeMount, ref } from 'vue'
import router, { PageList } from '@/vuejs/router'

import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import InputField from '@/vuejs/modules/shared/formfields/InputField.vue'
import LabelField from '@/vuejs/modules/shared/formfields/LabelField.vue'

import { useUserStore } from '@/vuejs/stores/user'

const isLoading = ref<boolean>(false)
const userStore = useUserStore()

onBeforeMount(() => {
  userStore.setEditingSubAccount()
})

const onDetailsFormSubmit = async () => {
  if (
    userStore.editingInfo.lastName === userStore.user.lastName &&
    userStore.editingInfo.firstName === userStore.user.firstName &&
    userStore.editingInfo.phone === userStore.user.account.phone
  ) {
    onCancelClick()
  } else {
    isLoading.value = true
    await userStore.updateUserAccountDetails()
    isLoading.value = false
  }
}

const onCancelClick = () => {
  router.push({
    name: PageList.ACCOUNT,
  })
}
</script>
