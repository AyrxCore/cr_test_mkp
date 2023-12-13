<template>
  <template v-if="!userAccounts.length">
    <div class="flex flex-col justify-between md:flex-row md:space-x-8">
      <div class="flex w-full flex-col md:w-1/2">
        <div v-if="showAlert" class="lg:w-5/6">
          <AlertSharedComponent />
        </div>
        <form class="w-full" @submit.prevent="loginSubmit">
          <h1 class="text-2xl font-bold text-primary xl:text-4xl">Bonjour</h1>
          <div class="mt-3 text-lg text-gray-500">
            Déjà adhérent ? Connectez-vous ici
          </div>
          <div class="lg:w-5/6">
            <div class="relative mb-3 mt-3">
              <input
                id="login-email"
                v-model="username"
                type="email"
                class="input !pr-16"
                placeholder="Adresse e-mail"
                :disabled="isLoading"
                required
              />
              <span
                class="absolute inset-y-0 right-0 flex items-center pr-8 text-gray-500"
              >
                <MailIcon />
              </span>
            </div>
            <div class="relative mb-3 mt-3">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Mot de passe"
                class="input !pr-16"
                :disabled="isLoading"
                required
              />
              <span
                class="absolute inset-y-0 right-0 flex cursor-pointer items-center pr-8 text-gray-500"
                @click="toggleShowPassword"
              >
                <EyeSlashIcon v-if="showPassword" />
                <EyeIcon v-else />
              </span>
            </div>
            <div class="mb-3 mt-3 flex justify-between">
              <ButtonComponent
                :is-loading="isLoading"
                type="submit"
                class="button-secondary-outline hover:bg-secondary"
              >
                Me connecter
              </ButtonComponent>
              <a href="/login/first-signin" class="button button-gradient">
                Ma première connexion
              </a>

              <a
                href="/login/reset-password"
                class="ml-2 text-sm font-medium text-gray-500"
              >
                Mot de passe oublié ?
              </a>
            </div>
            <p class="text-gray-500">
              Une question ? Appelez-nous :
              <a
                :href="`tel:${channel?.phoneNumber}`"
                class="text-secondary underline lg:text-right"
                >{{ channelPhoneNumber }}</a
              >
            </p>
          </div>
        </form>
      </div>
      <div class="flex sm:w-1/2">
        <AchetonsEnsembleComponent />
      </div>
    </div>
  </template>
  <template v-else-if="userAccounts.length > 1">
    <div class="flex flex-col justify-between md:flex-row md:space-x-8">
      <div class="flex w-full flex-col md:w-1/2">
        <div class="lg:w-5/6">
          <div v-if="showAlert && !showCGUModal">
            <AlertSharedComponent />
          </div>
          <div class="mb-5">
            <h1 class="home-title text-gradient">
              Bonjour {{ userFirstName }}
            </h1>
            <h3 class="text-gray-500">
              Veuillez sélectionner le compte acheteur avec lequel vous
              souhaitez être connecté
            </h3>
          </div>
          <div v-for="(account, id) in userAccounts" :key="id">
            <label
              v-if="account.externalApiData"
              :for="`account-radio-${id}`"
              class="mb-3 flex cursor-pointer items-center rounded-md bg-white px-4 py-2"
            >
              <input
                :id="`account-radio-${id}`"
                v-model="accountSelectedId"
                :value="account.id"
                type="radio"
                class="mr-4"
                @change="onChangeBuyer(account.acceptCGU)"
              />
              <div>
                <div class="font-bold uppercase text-primary">
                  {{ account.externalApiData.name }}
                </div>
                <div
                  v-if="account.externalApiData.number"
                  class="font-bold text-gray-500"
                >
                  SIRET : {{ account.externalApiData.number }}
                </div>
              </div>
            </label>
          </div>
          <div class="flex justify-end">
            <ButtonComponent
              :is-loading="isLoading"
              type="button"
              class="button-gradient w-full lg:w-auto"
              @click="onAccountClick"
            >
              <ArrowRightIcon />
              Valider
            </ButtonComponent>
          </div>
        </div>
      </div>
      <div class="flex sm:w-1/2">
        <AchetonsEnsembleComponent />
      </div>
    </div>
  </template>
  <CGUModal v-if="showCGUModal" class="modal" @valid-cgu="valideCGU" />
</template>
<script lang="ts" setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'

import AchetonsEnsembleComponent from '@/vuejs/modules/shared/AchetonsEnsembleComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import ArrowRightIcon from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CGUModal from '@/vuejs/modules/login/component/CGUModal.vue'
import EyeIcon from '@/vuejs/modules/shared/icon/EyeIconComponent.vue'
import EyeSlashIcon from '@/vuejs/modules/shared/icon/EyeSlashIconComponent.vue'
import MailIcon from '@/vuejs/modules/shared/icon/MailIconComponent.vue'

import { getUrlParam } from '@/vuejs/services/utils'
import { AlertType } from '@/vuejs/types/Alert'
import { useAlertStore } from '@/vuejs/stores/alert'
import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'
import { Account } from '@/vuejs/types/Account'
import { getErrorMessage } from '@/vuejs/services/login'
import { LoginResponse } from '@/vuejs/types/User'

const username = ref<string>('')
const password = ref<string>('')
const userAccounts = ref<Account[]>([])
const isLoading = ref<boolean>(false)
const userStore = useUserStore()
const alertStore = useAlertStore()
const showPassword = ref<boolean>(false)
const accountSelectedId = ref<string>(null)
const accountAcceptCGU = ref(null)
const showCGUModal = ref<boolean>(false)

const { show: showAlert } = storeToRefs(alertStore)

const { channel, formattedPhoneNumber: channelPhoneNumber } = storeToRefs(
  useChannelStore(),
)

const loginSubmit = async () => {
  if (isLoading.value) return
  alertStore.setClose()
  isLoading.value = true
  const accounts = await userStore.authenticate({
    username: username.value,
    password: password.value,
  })

  if (!accounts.length) {
    isLoading.value = false

    alertStore.setShow(
      getErrorMessage(LoginResponse.UserEmptyAccount),
      AlertType.danger,
    )

    return false
  }

  if (accounts.length > 1) {
    userAccounts.value = accounts
  } else {
    const [firstAccount] = accounts

    accountSelectedId.value = firstAccount.id

    if (firstAccount.acceptCGU) {
      await selectAccount(accountSelectedId.value)
    } else {
      showCGUModal.value = true
    }
  }
  isLoading.value = false
}

const toggleShowPassword = () => {
  showPassword.value = !showPassword.value
}

const selectAccount = async (accountId) => {
  if (!accountId) {
    alertStore.setShow(
      'Vous devez sélectionner un compte acheteur pour vous connecter',
      AlertType.danger,
    )

    return
  }

  isLoading.value = true

  const select = await userStore.selectUserAccount(accountId)
  window.dataLayer?.push({
    event: 'login',
  })

  const target = getUrlParam('target')
  select && (document.location.href = target ? `/${target}` : '/')

  isLoading.value = false
}
const onAccountClick = async () => {
  if (!accountAcceptCGU.value && accountSelectedId.value) {
    showCGUModal.value = true
  } else {
    await selectAccount(accountSelectedId.value)
  }
}

const onChangeBuyer = (acceptCgu) => {
  accountAcceptCGU.value = acceptCgu
}

const valideCGU = async () => {
  showCGUModal.value = false
  await selectAccount(accountSelectedId.value)
}

const userFirstName = computed(() => {
  const [firstUserAccount] = userAccounts.value

  return firstUserAccount.user?.firstName || ''
})
</script>
