<template>
  <div v-if="alertStore.show">
    <AlertSharedComponent />
  </div>
  <template v-if="!userAccounts.length">
    <form @submit.prevent="loginSubmit">
      <div class="mt-3">
        <h1 class="text-primary text-xl font-bold">Bonjour</h1>
        <div class="gray mt-3">Déjà adhérent ? Connectez-vous ici</div>
      </div>
      <div class="mb-3 mt-3">
        <input
            v-model="username"
            type="email"
            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm
             text-gray-900 focus:border-blue-500 focus:ring-blue-500"
            placeholder="Adresse e-mail"
            required
        />
      </div>
      <div class="mb-3 mt-3">
        <input
            v-model="password"
            type="password"
            placeholder="Mot de passe"
            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm
             text-gray-900 focus:border-blue-500 focus:ring-blue-500"
            required
        />
      </div>
      <div class="mb-3 mt-3 flex justify-between">
        <a
            href="#"
            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
        >Mot de passe oublié</a
        >
        <a
            href="#"
            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
        >Première connexion</a
        >
      </div>
      <div class="mt-3 flex justify-end">
        <ButtonComponent
            type="submit"
            :is-loading="isLoading"
            :disabled="isLoading"
        >
          Me connecter
        </ButtonComponent>
      </div>
    </form>
  </template>
  <template v-else>
    <div>
      <h3 class="primary home-subtitle">Veuillez sélectionner le compte acheteur avec lequel vous souhaitez être connecté</h3>
    </div>
    <div v-for="(account, id) in userAccounts" :key="id">
      <DefaultButton
          class="text-cotext w-auto bg-purple-600 mr-2 mb-2 items-center rounded-md px-5 py-2.5 text-sm text-white"
          :is-loading="isLoading"
          :disabled="isLoading"
          @click="onAccountClick(account)"
      >
        {{account.upplerDatas.name}}
      </DefaultButton>
    </div>
  </template>
</template>
<script lang="ts" setup>
import { onMounted, ref, watch } from 'vue'

import { useUserStore } from '@/vuejs/stores/user'

import { useAlertStore } from '@/vuejs/stores/alert'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'

const username = ref<string>('')
const password = ref<string>('')
const userAccounts = ref<string[]>([])
const isLoading = ref<boolean>(false)
const userStore = useUserStore()
const alertStore = useAlertStore()

const loginSubmit = async () => {
  isLoading.value = true
  const accounts = await userStore.authenticate(
    { username: username.value, password: password.value }
  )
  accounts.length > 1
  ? userAccounts.value =  accounts
  : (document.location.href = '/app/home')
  isLoading.value = false
}

const onAccountClick = async(account) => {
  isLoading.value = true
  const select = await userStore.selectUserAccount(account.id)
  select &&(document.location.href = '/app/home')
  isLoading.value = false
}
</script>
