<template>

  <template v-if="!userAccounts.length">
    <div class="flex flex-col md:flex-row justify-between md:space-x-8">
      <div class="w-full md:w-1/2 flex flex-col">
        <div v-if="showAlert" class="lg:w-5/6">
          <AlertSharedComponent />
        </div>
        <form
          class="w-full"
          @submit.prevent="loginSubmit"
        >
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
                href="/premiere-connexion"
                class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-300"
              >Première connexion</a
              >
              <a
                href="/mot-de-passe-oublie"
                class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-300"
              >Mot de passe oublié ?</a
              >
            </div>
            <div class="mt-3 lg:items-center flex flex-col lg:mt-6 lg:grid lg:grid-cols-3 lg:justify-items-stretch w-full mb-6 space-y-5 lg:space-y-0 ">
              <div class="w-full lg:justify-self-start">
                <ButtonComponent
                  :is-loading="isLoading"
                  type="submit"
                  class="button-gradient min-w-[180px] w-full lg:w-auto"
                >
                  <ArrowRightIcon />
                  Me connecter
                </ButtonComponent>
              </div>
              <div class="text-gray-500 lg:justify-self-center">
                Vous n'êtes pas encore adhérent ?<br />
                <span class="text-secondary underline">
                    Et si on se rencontrait ?
                  </span>
              </div>

              <div class="flex flex-col text-gray-500 lg:justify-self-end">
                <span class="lg:text-right">Ou appelez nous directement au </span>
                <span class="text-secondary underline lg:text-right">{{ PHONE_ANIMATION }}</span>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="sm:w-1/2 flex">
        <AchetonsEnsembleComponent />
      </div>
    </div>

  </template>
  <template v-else>
    <div class="flex flex-col md:flex-row justify-between md:space-x-8">
      <div class="w-full md:w-1/2 flex flex-col">
        <div class="lg:w-5/6">
          <div v-if="showAlert">
            <AlertSharedComponent />
          </div>
          <div class="mb-5">
            <h1 class="home-subtitle text-gradient">
              Bonjour {{ userAccounts[0].upplerDatas ? userAccounts[0].upplerDatas.master_user.firstname : ''}}
            </h1>
            <h3 class="text-gray-500">
              Veuillez sélectionner le compte acheteur avec lequel vous souhaitez être
              connecté
            </h3>
          </div>
          <div
            v-for="(account, id) in userAccounts"
            :key="id"
          >
            <div
              v-if="account.upplerDatas"
              class="bg-white rounded-md mb-3 p-2 flex flex-col h-[60px]"
            >
              <div>
                <input
                  v-model="accountRadio"
                  name="accountRadio"
                  type="radio"
                  :value="account.id"
                  class="mr-1"
                />
                <label class="text-primary font-bold uppercase">
                  {{ account.upplerDatas.name }}
                </label>
              </div>

              <label
                v-if="account.upplerDatas.number"
                class="text-gray-500 ml-5 font-bold"
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
      <div class="sm:w-1/2 flex">
        <AchetonsEnsembleComponent />
      </div>
    </div>
  </template>
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

const username = ref<string>('')
const password = ref<string>('')
const userAccounts = ref<string[]>([])
const isLoading = ref<boolean>(false)
const userStore = useUserStore()
const alertStore = useAlertStore()
const showPassword = ref<boolean>(false)
const accountRadio = ref<string>(null)

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
  console.log(accounts)
  if (accounts.length > 1) {
    userAccounts.value = accounts
    isLoading.value = false
  } else {
    document.location.href = '/app/home-page'
  }
}

const toggleShowPassword = () => {
  showPassword.value = !showPassword.value
}
const onAccountClick = async () => {
  isLoading.value = true
  if (accountRadio.value) {
    const select = await userStore.selectUserAccount(accountRadio.value)
    select && (document.location.href = '/app/home-page')
    isLoading.value = false
  } else {
    isLoading.value = false
    alertStore.setShow(
      'Vous devez sélectionner un compte acheteur pour vous connecter',
      AlertType.danger,
    )

  }
}
</script>
