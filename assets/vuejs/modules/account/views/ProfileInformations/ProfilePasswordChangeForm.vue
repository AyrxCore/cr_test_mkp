<template>
  <AccountPage>
    <template #right-side>
      <h3 class="mb-4 mt-2 text-title-35 text-primary md:mt-0">
        Modifier mon mot de passe
      </h3>
      <div
        v-show="passwordError !== ''"
        class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700 dark:bg-red-200 dark:text-red-800"
        role="alert"
      >
        {{ passwordError }}
      </div>

      <form @submit.prevent="onPasswordFormSubmit">
        <div class="grid-rows grid grid-flow-col gap-6">
          <div class="mb-6">
            <LabelField title="Mot de passe actuel" />
            <InputField v-model="currentPassword" type="password" required />
          </div>
        </div>
        <div class="grid-rows grid grid-flow-col gap-6">
          <div class="mb-6">
            <LabelField title="Nouveau mot de passe" />
            <InputField
              v-model="newPassword"
              type="password"
              required
              :class="classes"
            />
          </div>
        </div>
        <div class="grid-rows grid grid-flow-col gap-6">
          <div class="mb-6">
            <LabelField title="Confirmation" />
            <InputField
              v-model="confirmation"
              type="password"
              required
              :class="classes"
            />
          </div>
        </div>
        <div class="flex justify-end">
          <ButtonComponent
            class="button-secondary-outline mr-2"
            type="button"
            @click="onCancelClick"
          >
            Annuler
          </ButtonComponent>
          <ButtonComponent
            class="button-secondary-outline"
            :is-loading="isLoading"
          >
            Enregistrer
          </ButtonComponent>
        </div>
      </form>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import { computed, ref } from 'vue'
import router, { PageList } from '@/vuejs/router'

import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import InputField from '@/vuejs/modules/shared/formfields/InputField.vue'
import LabelField from '@/vuejs/modules/shared/formfields/LabelField.vue'

import { useUserStore } from '@/vuejs/stores/user'

const currentPassword = ref<string>('')
const newPassword = ref<string>('')
const confirmation = ref<string>('')
const passwordError = ref<string>('')
const userStore = useUserStore()
const isLoading = ref<boolean>(false)

const classes = computed((): string => {
  if (newPassword.value !== '' || confirmation.value !== '') {
    return newPassword.value !== confirmation.value
      ? 'border-2 border-rose-600'
      : 'border-2 border-green-600'
  }

  return ''
})

const onPasswordFormSubmit = async () => {
  if (newPassword.value !== confirmation.value) {
    passwordError.value =
      'Le mot de passe et sa confirmation doivent être identiques'
    return
  }
  isLoading.value = true
  await userStore.updateUserPassword({
    currentPassword: currentPassword.value,
    password: newPassword.value,
    confirmation: confirmation.value,
  })

  isLoading.value = false
}

const onCancelClick = () => {
  router.push({
    name: PageList.ACCOUNT,
  })
}
</script>
