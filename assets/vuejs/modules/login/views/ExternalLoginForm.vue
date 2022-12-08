<template>
  <div v-if="alertStore.show">
    <AlertSharedComponent />
  </div>
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
        required
      />
    </div>
    <div class="mb-3 mt-3">
      <input
        v-model="password"
        type="password"
        placeholder="Mot de passe"
        class="input"
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
<script lang="ts" setup>
import { onMounted, ref, watch } from 'vue'

import { useUserStore } from '@/vuejs/stores/user'

import { useAlertStore } from '@/vuejs/stores/alert'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import ArrowRightIcon from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

const username = ref<string>('')
const password = ref<string>('')
const isLoading = ref<boolean>(false)
const userStore = useUserStore()
const alertStore = useAlertStore()

const loginSubmit = async () => {
  isLoading.value = true
  await userStore.authenticate(
    { email: username.value, password: password.value },
    true,
  )
  isLoading.value = false
}
</script>
