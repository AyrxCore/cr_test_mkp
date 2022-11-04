<template>
    <div v-if="alertStore.show">
      <AlertSharedComponent/>
    </div>
    <form
        @submit.prevent="loginSubmit"
    >
      <div class="mt-3">
        <h1 class="primary text-xl font-bold">Bonjour</h1>
        <div class="gray mt-3">Déjà adhérent ? Connectez-vous ici</div>
      </div>
      <div class="mb-3 mt-3">
        <input
            v-model="username"
            type="email"
            class="bg-gray-50 border border-gray-300 text-gray-900
           text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
            placeholder="Adresse e-mail"
            required
        />
      </div>
      <div class="mb-3 mt-3">
        <input
            v-model="password"
            type="password"
            placeholder="Mot de passe"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
          focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
            required
        />
      </div>
      <div class="flex items-start mb-3 mt-3">
        <div class="flex items-center h-5">
          <input
              type="checkbox"
              value=""
              class="w-4 h-4 bg-gray-50 rounded border border-gray-300 focus:ring-3 focus:ring-blue-300"
          />
        </div>
        <label for="remember" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Mot de passe oublié ?</label>
      </div>
      <div class="mt-3 flex justify-end">
        <button
            type="submit"
            class="text-white bg-gradient-to-r from-purple-600 via-cyan-500 to-cyan-600 flex
                hover:bg-gradient-to-br focus:ring-4  focus:outline-none focus:ring-cyan-300 dark:focus:ring-cyan-800
                font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2"
        >
          <LoaderSharedComponent
              v-show="isLoading"
              class="mr-2"
          />
          Me connecter
        </button>
      </div>
    </form>
</template>
<script lang="ts" setup>
import {ref} from 'vue'

import {useUserStore} from '@/vuejs/stores/user'

import {useAlertStore} from '@/vuejs/stores/alert'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import router, { PageList } from '@/vuejs/router'

const username = ref<string>('')
const password = ref<string>('')
const isLoading = ref<boolean>(false)
const userStore = useUserStore()
const alertStore = useAlertStore()

const loginSubmit = async () => {
  isLoading.value = true
  const login = await userStore.authenticate({email: username.value, password: password.value})
  login  && router.push({ name: PageList.HOME_PAGE })
  isLoading.value = false
}

const  onClick = async () => {
 await userStore.getCurrentUserDatas()
}
</script>
