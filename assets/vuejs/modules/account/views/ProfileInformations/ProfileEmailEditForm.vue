<template>
  <AccountPage>
    <template #right-side>
      <h3 class="text-title-primary mb-4 mt-2 xl:mt-0">
        Modifier mon email de contact
      </h3>
      <form ref="formEmailAccount">
        <div class="grid-rows grid grid-flow-col gap-6">
          <div class="mb-6">
            <LabelField title="Email" />
            <InputField
              v-model.trim="userStore.editingInfo.username"
              type="email"
              required
              title="Ex: votre_email@test.fr"
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
          <ButtonComponent class="button-primary mr-2" type="submit">
            Enregistrer
          </ButtonComponent>

          <ChangeEmailConfirmationModal
            v-if="showConfirmationModal"
            class="modal"
            @cancel="onCancelModalClick"
            @validate="onEmailFormSubmit"
          />
          <ChangeEmailResultModal
            v-if="showResultModal"
            class="modal"
            @cancel="onCancelModalClick"
            @validate="onEmailFormSubmit"
          />
        </div>
      </form>
    </template>
  </AccountPage>
</template>

<script lang="ts" setup>
import { onBeforeMount, onMounted, ref } from 'vue'
import router, { PageList } from '@/vuejs/router'

import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import InputField from '@/vuejs/modules/shared/formfields/InputField.vue'
import LabelField from '@/vuejs/modules/shared/formfields/LabelField.vue'
import ChangeEmailConfirmationModal from '@/vuejs/modules/account/views/ProfileInformations/ChangeEmailConfirmationModal.vue'
import ChangeEmailResultModal from '@/vuejs/modules/account/views/ProfileInformations/ChangeEmailResultModal.vue'

import { useUserStore } from '@/vuejs/stores/user'

const isLoading = ref<boolean>(false)
const userStore = useUserStore()
const showConfirmationModal = ref<boolean>(false)
const showResultModal = ref<boolean>(false)
const formEmailAccount = ref(null)

onBeforeMount(() => {
  userStore.setEditingAccount()
})

const onEmailFormSubmit = async () => {
  router.push({ name: PageList.ACCOUNT })
  isLoading.value = true
  await userStore.updateUserAccountEmail()
  showResultModal.value = true

  isLoading.value = false
}

const onCancelModalClick = () => {
  showConfirmationModal.value = false
}

const onCancelClick = () => {
  router.push({
    name: PageList.ACCOUNT,
  })
}
const onValidateClick = () => {
  if (userStore.editingInfo.username === userStore.user.username) {
    onCancelClick()
  } else {
    showConfirmationModal.value = true
  }
}

const processForm = (e) => {
  if (e.preventDefault) {
    onValidateClick()
    e.preventDefault()
  }
}

onMounted(() => {
  if (formEmailAccount.value.attachEvent) {
    formEmailAccount.value.attachEvent('submit', processForm)
  } else {
    formEmailAccount.value.addEventListener('submit', processForm)
  }
})
</script>
