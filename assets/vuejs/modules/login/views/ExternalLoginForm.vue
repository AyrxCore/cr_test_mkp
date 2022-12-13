<template>
  <div v-if="alertStore.show">
    <AlertSharedComponent />
  </div>
  <template v-if="!userAccounts.length">
  <form @submit.prevent="loginSubmit">
    <h1 class="text-2xl font-bold text-primary sm:text-4xl">Bonjour</h1>
    <div class="mt-3 text-lg text-gray-500">
      Déjà adhérent ? Connectez-vous ici
    </div>
    <div class="mb-3 mt-3">
      <input
        v-model="username"
        type="email"
        class="input"
        placeholder="Adresse e-mail"
        :disabled="isLoading"
        required
      />
    </div>
    <div class="mb-3 mt-3">
      <input
        v-model="password"
        type="password"
        placeholder="Mot de passe"
        class="input"
        :disabled="isLoading"
        required
      />
    </div>
    <a href="#" class="text-gray-500 underline">Mot de passe oublié ? </a>
    <div class="mt-3 items-center sm:mt-6 sm:flex">
      <ButtonComponent
        :is-loading="isLoading"
        type="submit"
        class="button-gradient min-w-[180px]"
      >
        <ArrowRightIcon />
        Me connecter
      </ButtonComponent>

      <div class="text-gray-500 sm:ml-10 sm:flex">
        <div class="pt-6 sm:pt-0">
          Vous n'êtes pas encore adhérent ?<br />
          <span class="text-secondary underline">
            Et si on se rencontrait ?
          </span>
        </div>
        <div class="py-6 sm:pt-0">
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
        Veuillez sélectionner le compte acheteur avec lequel vous souhaitez être connecté
      </h3>
    </div>
    <div v-for="(account, id) in userAccounts" :key="id">
      <ButtonComponent
          class="text-cotext w-auto bg-purple-600 mr-2 mb-2 items-center rounded-md px-5 py-2.5 text-sm text-white"
          :disabled="isLoading"
          @click="onAccountClick(account)"
      >
        {{account.upplerDatas.name}}
      </ButtonComponent>
    </div>
    <div v-show="isLoading">
      <LoaderSharedComponent/>
    </div>
  </template>
</template>
<script lang="ts" setup>
import { onMounted, ref, watch } from 'vue'

import { useUserStore } from '@/vuejs/stores/user'

import { useAlertStore } from '@/vuejs/stores/alert'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import LoaderSharedComponent from "@/vuejs/modules/shared/LoaderSharedComponent.vue";
import ArrowRightIcon from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

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

  if(accounts.length === 0) {
    isLoading.value = false
    return false
  }

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
