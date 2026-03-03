<template>
  <AccountPage>
    <template #right-side>
      <h3 class="text-title-primary mb-4 mt-2 xl:mt-0">
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
            <PasswordInputField v-model="currentPassword" required />
          </div>
        </div>
        <div class="grid-rows grid grid-flow-col gap-6">
          <div class="mb-6">
            <LabelField title="Nouveau mot de passe" />
            <PasswordInputField
              v-model="newPassword"
              :classes="classes"
              required
              @focus="newPasswordFocused = true"
              @blur="newPasswordFocused = false"
            />
            <PasswordStrengthChecklist
              :password="newPassword"
              :focused="newPasswordFocused"
            />
          </div>
        </div>
        <div class="grid-rows grid grid-flow-col gap-6">
          <div class="mb-6">
            <LabelField title="Confirmation" />
            <PasswordInputField
              v-model="confirmation"
              :classes="classes"
              required
            />
          </div>
        </div>
        <div class="flex justify-end">
          <ButtonComponent
            class="button-primary-outline mr-2"
            type="button"
            @click="onCancelClick"
          >
            Annuler
          </ButtonComponent>
          <ButtonComponent :is-loading="isLoading" class="button-primary">
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
import { useUserStore } from '@/vuejs/stores/user'
import { isPasswordValid } from '@/vuejs/composables/usePasswordStrength'

import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import LabelField from '@/vuejs/modules/shared/formfields/LabelField.vue'
import PasswordInputField from '@/vuejs/modules/shared/formfields/PasswordInputField.vue'
import PasswordStrengthChecklist from '@/vuejs/modules/shared/formfields/PasswordStrengthChecklist.vue'

const currentPassword = ref<string>('')
const newPassword = ref<string>('')
const confirmation = ref<string>('')
const passwordError = ref<string>('')
const newPasswordFocused = ref<boolean>(false)
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
  if (!isPasswordValid(newPassword.value)) {
    passwordError.value =
      'Le mot de passe ne respecte pas les consignes de sécurité'
    return
  }
  if (newPassword.value !== confirmation.value) {
    passwordError.value =
      'Le mot de passe et sa confirmation doivent être identiques'
    return
  }
  isLoading.value = true
  const error = await userStore.updateUserPassword({
    currentPassword: currentPassword.value,
    password: newPassword.value,
    confirmation: confirmation.value,
  })

  if (error) {
    passwordError.value = error
  }

  isLoading.value = false
}

const onCancelClick = () => {
  router.push({
    name: PageList.ACCOUNT,
  })
}
</script>
