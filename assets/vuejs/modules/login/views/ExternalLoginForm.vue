<template>
  <div v-if="alertStore.show">
    <AlertSharedComponent />
  </div>
  <template v-if="!userAccounts.length">
    <form @submit.prevent="loginSubmit">
      <h1 class="text-2xl font-bold text-primary xl:text-4xl">Bonjour</h1>
      <div class="mt-3 text-lg text-gray-500">
        Déjà adhérent ? Connectez-vous ici
      </div>
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
          type="password"
          placeholder="Mot de passe"
          class="input !pr-16"
          :disabled="isLoading"
          required
        />
        <span
          class="absolute inset-y-0 right-0 flex items-center pr-8 text-gray-500"
        >
          <EyeIcon />
        </span>
      </div>
      <div class="mb-3 mt-3 flex justify-between">
        <a
          href="/mot-de-passe-oublie"
          class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
          >Mot de passe oublié ?</a
        >
        <a
          href="/premiere-connexion"
          class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
          >Première connexion</a
        >
      </div>
      <div class="mt-3 items-center lg:mt-6 lg:flex">
        <ButtonComponent
          :is-loading="isLoading"
          type="submit"
          class="button-gradient min-w-[180px]"
        >
          <ArrowRightIcon />
          Me connecter
        </ButtonComponent>

        <div class="text-gray-500 lg:ml-10 lg:flex">
          <div class="pt-6 lg:pt-0">
            Vous n'êtes pas encore adhérent ?<br />
            <span class="text-secondary underline">
              Et si on se rencontrait ?
            </span>
          </div>
          <div class="py-6 xl:pt-0">
            Ou appelez nous directement au <br />
            <span class="text-secondary underline">04.37.65.06.21</span>
          </div>
        </div>
      </div>
    </form>
  </template>
  <template v-else>
    <div>
      <h3 class="primary home-subtitle">
        Veuillez sélectionner le compte acheteur avec lequel vous souhaitez être
        connecté
      </h3>
    </div>
    <div v-for="(account, id) in userAccounts" :key="id">
      <ButtonComponent
        class="text-cotext mr-2 mb-2 w-auto items-center rounded-md bg-purple-600 px-5 py-2.5 text-sm text-white"
        :disabled="isLoading"
        @click="onAccountClick(account)"
      >
        {{ account.upplerDatas.name }}
      </ButtonComponent>
    </div>
    <div v-show="isLoading">
      <LoaderSharedComponent />
    </div>
  </template>
</template>
<script lang="ts" setup>
import { ref } from 'vue'

import { useUserStore } from '@/vuejs/stores/user'

import { useAlertStore } from '@/vuejs/stores/alert'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import ArrowRightIcon from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import MailIcon from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import EyeIcon from '@/vuejs/modules/shared/icon/EyeIconComponent.vue'

const username = ref<string>('')
const password = ref<string>('')
const userAccounts = ref<string[]>([])
const isLoading = ref<boolean>(false)
const userStore = useUserStore()
const alertStore = useAlertStore()

const loginSubmit = async () => {
  isLoading.value = true
  const accounts = await userStore.authenticate({
    username: username.value,
    password: password.value,
  })

  if (accounts.length === 0) {
    isLoading.value = false
    return false
  }

  if(accounts.length > 1) {
    userAccounts.value =  accounts
    isLoading.value = false
  } else {
     document.location.href = '/app/home'
  }
}

const onAccountClick = async (account) => {
  isLoading.value = true
  const select = await userStore.selectUserAccount(account.id)
  select && (document.location.href = '/app/home')
  isLoading.value = false
}
</script>
