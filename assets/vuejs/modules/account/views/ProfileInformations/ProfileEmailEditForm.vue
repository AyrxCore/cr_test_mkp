<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary mb-4 page-principal-title mt-2 md:mt-0">Modifier mon email de contact</h3>
      <form
          @submit.prevent="onEmailFormSubmit"
      >
        <div class="grid grid-rows grid-flow-col gap-6">
          <div  class="mb-6">
            <LabelField title="Email"/>
            <InputField
                v-model="userStore.user.account.editingSubAccount.email"
                type="email"
                required="true"
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
import {onBeforeMount, onMounted, ref} from 'vue'
import router, {PageList} from '@/vuejs/router'
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import {useUserStore} from '@/vuejs/stores/user'

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
    name: PageList.ACCOUNT
  })
}
</script>
