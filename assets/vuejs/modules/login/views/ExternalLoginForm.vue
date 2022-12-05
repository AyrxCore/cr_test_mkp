<template>
  <div v-if="alertStore.show">
    <AlertSharedComponent />
  </div>
  <form @submit.prevent="loginSubmit">
    <div class="mt-3">
      <h1 class="text-xl font-bold text-primary">Bonjour</h1>
      <div class="gray mt-3">Déjà adhérent ? Connectez-vous ici</div>
    </div>
    <div class="mb-3 mt-3">
      <input
        v-model="username"
        type="email"
        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
        placeholder="Adresse e-mail"
        required
      />
    </div>
    <div class="mb-3 mt-3">
      <input
        v-model="password"
        type="password"
        placeholder="Mot de passe"
        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
        required
      />
    </div>
    <div class="mb-3 mt-3 flex items-start">
      <div class="flex h-5 items-center">
        <input
          type="checkbox"
          value=""
          class="focus:ring-3 h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300"
        />
      </div>
      <label
        for="remember"
        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
        >Mot de passe oublié ?</label
      >
    </div>
    <div class="mt-3 flex justify-end">
      <ButtonComponent
        :is-loading="isLoading"
        type="submit"
        class="button-gradient"
      >
        Me connecter
      </ButtonComponent>
    </div>
  </form>
</template>
<script lang="ts" setup>
import { onMounted, ref, watch } from 'vue'

import { useUserStore } from '@/vuejs/stores/user'

import { useAlertStore } from '@/vuejs/stores/alert'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'

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
