<template>
  <nav class="bg-white py-2 md:py-4 header px-4" >
    <div class=" px-4 mx-auto md:flex md:items-center">

      <div class="">
        <div class="w-full flex justify-end">
          <img src="/assets/img/logo_fond_transparent.png" alt="" width="25"/>
        </div>
        <div>
          <a href="#" class="font-bold text-xl text-white">QANTIS</a>
        </div>
        <div class="secondary text-sm">Marketplace</div>
        <button
            class="border border-solid border-gray-600 px-3 py-1 rounded text-gray-600 opacity-50 hover:opacity-75 md:hidden"
        >
          <i class="fas fa-bars"/>
        </button>
      </div>

      <div id="navbar-collapse" class="hidden md:flex flex-col md:flex-row md:ml-auto mt-3 md:mt-0">
        <div v-if="user !== null" class="text-white">
          <div>Bonjour {{ user.email }}</div>
          <div>Mon compte</div>
        </div>

      </div>
    </div>
  </nav>

</template>

<script lang="ts" setup>

import {useUserStore} from '@/vuejs/stores/user'
import {storeToRefs} from 'pinia'


const userStore = useUserStore()
const {user} = storeToRefs(userStore)

</script>
