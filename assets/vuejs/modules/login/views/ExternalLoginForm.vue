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
                v-model="username"
                type="email"
                class="input !pr-16"
                id="login-email"
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
              <a
                href="/login/first-signin"
                class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-300"
              >
                Première connexion
              </a>
              <a
                href="/login/reset-password"
                class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-300"
              >
                Mot de passe oublié ?
              </a>
            </div>
            <div
              class="mt-3 mb-6 flex w-full flex-col space-y-5 lg:mt-6 lg:grid lg:grid-cols-3 lg:items-center lg:justify-items-stretch lg:space-y-0"
            >
              <div class="w-full lg:justify-self-start">
                <ButtonComponent
                  :is-loading="isLoading"
                  type="submit"
                  class="button-gradient w-full min-w-[180px] lg:w-auto"
                >
                  <ArrowRightIcon />
                  Me connecter
                </ButtonComponent>
              </div>
              <div class="text-gray-500 lg:justify-self-center">
                Vous n'êtes pas encore adhérent ?<br />
                <a
                  href="https://qantis.co/contact"
                  class="text-secondary underline"
                  target="_blank"
                >
                  Et si on se rencontrait ?
                </a>
              </div>

              <div class="flex flex-col text-gray-500 lg:justify-self-end">
                <span class="lg:text-right">
                  Ou appelez nous directement au
                </span>
                <a
                  :href="`tel:${PHONE_ANIMATION}`"
                  class="text-secondary underline lg:text-right"
                >
                  {{ PHONE_ANIMATION }}
                </a>
              </div>
            </div>
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
            <h1 class="home-subtitle text-gradient">
              Bonjour
              {{
                userAccounts[0]._user.firstName
                  ? userAccounts[0]._user.firstName
                  : ''
              }}
            </h1>
            <h3 class="text-gray-500">
              Veuillez sélectionner le compte acheteur avec lequel vous
              souhaitez être connecté
            </h3>
          </div>
          <div v-for="(account, id) in userAccounts" :key="id">
            <div
              v-if="account.upplerDatas"
              class="mb-3 flex h-[60px] flex-col rounded-md bg-white p-2"
            >
              <div>
                <input
                  v-model="accountSelectedId"
                  name="accountRadio"
                  type="radio"
                  :value="account.id"
                  class="mr-1"
                  @change="onChangeBuyer(account.acceptCGU)"
                />
                <label class="font-bold uppercase text-primary">
                  {{ account.upplerDatas.name }}
                </label>
              </div>

              <label
                v-if="account.upplerDatas.number"
                class="ml-5 font-bold text-gray-500"
              >
                SIRET: {{ account.upplerDatas.number }}
              </label>
            </div>
          </div>
          <div class="flex justify-end">
            <button
              class="button button-gradient w-full lg:w-auto"
              :disabled="isLoading"
              @click="onAccountClick"
            >
              <div v-show="isLoading">
                <LoaderSharedComponent />
              </div>
              <ArrowRightIcon />
              Valider
            </button>
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
import { ref } from 'vue'
import { storeToRefs } from 'pinia'

import { useUserStore } from '@/vuejs/stores/user'

import { useAlertStore } from '@/vuejs/stores/alert'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import ArrowRightIcon from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import MailIcon from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import EyeIcon from '@/vuejs/modules/shared/icon/EyeIconComponent.vue'
import EyeSlashIcon from '@/vuejs/modules/shared/icon/EyeSlashIconComponent.vue'
import { PHONE_ANIMATION } from '@/vuejs/services/const'
import AchetonsEnsembleComponent from '@/vuejs/modules/shared/AchetonsEnsembleComponent.vue'
import { AlertType } from '@/vuejs/types/Alert'
import CGUModal from '@/vuejs/modules/login/component/CGUModal.vue'

const username = ref<string>('')
const password = ref<string>('')
const userAccounts = ref<string[]>([])
const isLoading = ref<boolean>(false)
const userStore = useUserStore()
const alertStore = useAlertStore()
const showPassword = ref<boolean>(false)
const accountSelectedId = ref<string>(null)
const accountAcceptCGU = ref(null)
const showCGUModal = ref<boolean>(false)

const { show: showAlert } = storeToRefs(alertStore)

const loginSubmit = async () => {
  if (isLoading.value) return
  alertStore.setClose()
  isLoading.value = true
  const accounts = await userStore.authenticate({
    username: username.value,
    password: password.value,
  })

  if (accounts.length === 0) {
    isLoading.value = false
    return false
  }

  if (accounts.length > 1) {
    userAccounts.value = accounts
  } else {
    accountSelectedId.value = accounts[0].id
    if (accounts[0].acceptCGU) {
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
  isLoading.value = true
  if (accountId) {
    const select = await userStore.selectUserAccount(accountId)
    select && (document.location.href = '/')
    isLoading.value = false
  } else {
    isLoading.value = false
    alertStore.setShow(
      'Vous devez sélectionner un compte acheteur pour vous connecter',
      AlertType.danger,
    )
  }
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
</script>
