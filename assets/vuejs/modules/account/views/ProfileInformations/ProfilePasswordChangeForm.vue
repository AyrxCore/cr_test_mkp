<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary mb-4 text-title-35 mt-2 md:mt-0">Modifier mon mot de passe</h3>
      <div
          v-show="passwordError !== ''"
          class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert"
      >
        {{ passwordError }}
      </div>

      <form
          @submit.prevent="onPasswordFormSubmit"
      >
        <div class="grid grid-rows grid-flow-col gap-6">
          <div  class="mb-6">
            <LabelField title="Mot de passe actuel"/>
            <InputField
                v-model="currentPassword"
                type="password"
                required="true"
            />
          </div>
        </div>
        <div class="grid grid-rows grid-flow-col gap-6">
          <div  class="mb-6">
            <LabelField title="Nouveau mot de passe"/>
            <InputField
                v-model="newPassword"
                type="password"
                required="true"
                :class="classes"
            />
          </div>
        </div>
        <div class="grid grid-rows grid-flow-col gap-6">
          <div  class="mb-6">
            <LabelField title="Confirmation"/>
            <InputField
                v-model="confirmation"
                type="password"
                required="true"
                :class="classes"
            />
          </div>
        </div>
        <div class="flex justify-end">
          <ButtonComponent
              class="default-button mr-2 mb-2 flex items-center px-4 py-5 text-sm font-medium bg-transparent
             !text-purple-500 rounded-full border border-purple-600"
              type="button"
              @click="onCancelClick"
          >
            Annuler
          </ButtonComponent>
          <ButtonComponent
              class="default-button mr-2 mb-2 flex items-center px-4 py-5 text-sm font-medium bg-transparent
             !text-purple-500 rounded-full border border-purple-600"
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
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import InputField from '@/vuejs/modules/shared/formfields/InputField.vue'
import LabelField from '@/vuejs/modules/shared/formfields/LabelField.vue'
import {computed, ref} from 'vue'
import router, {PageList} from '@/vuejs/router'
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import {useUserStore} from '@/vuejs/stores/user'

const currentPassword = ref<string>('')
const newPassword = ref<string>('')
const confirmation = ref<string>('')
const passwordError = ref<string>('')
const userStore = useUserStore()
const isLoading = ref<boolean>(false)

const classes = computed((): string => {
  if(newPassword.value !== '' || confirmation.value !== '') {
    return newPassword.value !== confirmation.value
        ? 'border-2 border-rose-600'
        : 'border-2 border-green-600'
  }

  return ''

})

const onPasswordFormSubmit = async () => {
  if(newPassword.value !== confirmation.value) {
    passwordError.value = 'Le mot de passe et sa confirmation doivent être identiques'
    return
  }
  isLoading.value = true
  await userStore.updateUserPassword({
    currentPassword: currentPassword.value,
    password: newPassword.value,
    confirmation: confirmation.value
  })

  isLoading.value = false
}

const onCancelClick = () => {
  router.push({
    name: PageList.ACCOUNT
  })
}
</script>
