<template>
  <div
    class="gap-4"
  >
    <div class="rounded-lg lg:bg-white p-3 lg:p-7">
      <h3 class="text-primary mb-2 text-[20px]">
        {{ user.firstName }}
        <span class="uppercase">{{ user.lastName }}</span>
      </h3>
      <p class="flex lg:flex-col text-gray-500">
        <span class="flex font-bold mr-2"> Qantis</span>
        <span class="flex"> Statut:</span>
      </p>
      <a
        href="#"
        class="text-gray-500 font-bold underline decoration-1.5 underline-offset-2 hover:decoration-secondary hidden lg:inline-flex"
        @click="onLogout"
      >
        Deconnexion
      </a>
    </div>
    <div class="rounded-lg lg:bg-white p-2.5 lg:p-7">
      <h3 class="text-primary lg:mb-2 text-[20px]">Mes contacts</h3>
      <p class="items-center text-[16px] text-gray-500">
        <span class="flex">
          <ChevronRightIconComponent :stroke-color="'#5E6875'" />
          Service adhérents
        </span>
        <span class="ml-6 font-bold">04 37 65 06 21</span>
      </p>
      <p class="mt-4 items-center text-base text-gray-500">
        <span class="flex">
          <ChevronRightIconComponent :stroke-color="'#5E6875'" />
          Votre animateur:
        </span>
        <span class="ml-6 lg:ml-6 lg:flex font-bold lg:font-normal"> Elise Gasse</span>
        <span class="ml-2 lg:ml-6 lg:flex font-bold">animation@qantis.co</span>
      </p>
    </div>
    <div
      v-for="(menu, key) in meenuItems"
      :key="key"
      class="rounded-lg lg:bg-white p-2.5 lg:pl-7"
    >
      <h3 class="text-primary lg:mb-2 text-[20px]">{{ menu.title }}</h3>
      <p
        v-for="(item, keyItem) in menu.items"
        :key="keyItem"
        class="mb-3 flex flex items-center"
      >
        <ChevronRightIconComponent :stroke-color="'#5E6875'" />
        <a
          :href="item.url"
          class="text-[16px] text-gray-500 underline decoration-1.5 underline-offset-2 hover:decoration-secondary"
          :class="{
                    'decoration-secondary': selectedTab === item.id,
                    'text-secondary': selectedTab === item.id,
                  }"
        >
          {{ item.name }}
        </a>
      </p>
    </div>
    <div class="sticky bottom-0 bg-white text-primary lg:mb-2 text-[20px] p-2.5 lg:p-0 lg:hidden bg-white">
      <a
        href="#"
        class="flex text-primary lg:hidden"
        @click="onLogout"
      >
        <DisconnectIconComponent class="mr-2" />
        Se déconnecter
      </a>
    </div>
  </div>
</template>

<script lang="ts" setup>
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import { ref } from 'vue'
import { AccountPageList, baseUrl } from '@/vuejs/modules/account/routerAccount'
import { useUserStore } from '@/vuejs/stores/user'
import { storeToRefs } from 'pinia'
import DisconnectIconComponent from '@/vuejs/modules/shared/icon/DisconnectIconComponent.vue'

const props = defineProps({
  selectedTab: {
    required: true,
    type: String,
  },
})

const userStore = useUserStore()
const { user } = storeToRefs(userStore)

const onLogout = async (e: Event): Promise<void> => {
  e.preventDefault()
  ;(await userStore.logout()) && location.reload()
}

const meenuItems = ref([
  {
    title: 'Mes commandes',
    items: [
      {
        name: 'Historiques de commandes',
        url: baseUrl + AccountPageList.ORDERS_HISTORY,
        id: AccountPageList.ORDERS_HISTORY,
      },
      {
        name: 'Factures',
        url: '',
        id: 'none',
      },
      {
        name: 'Paniers sauvegardés',
        url: baseUrl + AccountPageList.SAVED_CARTS,
        id: AccountPageList.SAVED_CARTS,
      },
      {
        name: 'Bons de livraison',
        url: '',
        id: 'none',
      },
      {
        name: 'Validation de commandes',
        url: baseUrl + AccountPageList.ORDERS_VALIDATION,
        id: AccountPageList.ORDERS_VALIDATION,
      },
    ],
  },
  {
    title: 'Mon profil',
    items: [
      {
        name: 'Coordonnées',
        url: baseUrl + AccountPageList.CONTACT_INFORMATION,
        id: AccountPageList.CONTACT_INFORMATION,
      },
      {
        name: 'Liste de produits favoris',
        url: baseUrl + AccountPageList.FAVORIS_LIST,
        id: AccountPageList.FAVORIS_LIST,
      },
    ],
  },
  {
    title: 'Mon organisation',
    items: [
      {
        name: 'Utilisateurs',
        url: '',
        id: 'none',
      },
      {
        name: 'Adresses',
        url: baseUrl + AccountPageList.ADDRESSES,
        id: AccountPageList.ADDRESSES,
      },
      {
        name: 'Directions - circuits de validation',
        url: '',
        id: 'none',
      },
      {
        name: 'Statuts',
        url: '',
        id: 'none',
      },
    ],
  },
  {
    title: 'Statistiques',
    items: [
      {
        name: 'Statistiques de consommation',
        url: '',
        id: 'none',
      },
    ],
  },
])
</script>

<style scoped>

</style>
