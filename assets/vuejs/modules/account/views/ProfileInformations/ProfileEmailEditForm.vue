<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary mb-4 mt-2 text-title-35 md:mt-0">
        Modifier mon email de contact
      </h3>
      <form @submit.prevent="onEmailFormSubmit">
        <div class="grid-rows grid grid-flow-col gap-6">
          <div class="mb-6">
            <LabelField title="Email" />
            <InputField
              v-model="userStore.user.account.editingSubAccount.email"
              type="email"
              required
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

const onEmailFormSubmit = async () => {
  isLoading.value = true
  await userStore.updateUserAccountEmail()
  isLoading.value = false
}

const onCancelClick = () => {
  router.push({
    name: PageList.ACCOUNT,
  })
}
</script>
