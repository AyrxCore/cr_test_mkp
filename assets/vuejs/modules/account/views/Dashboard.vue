<template>
  <AccountPage>
    <template #right-side>
      <div>
        <h3 class="primary text-title-primary mb-2 mt-2 lg:mt-0">
          Mes données de consommation
        </h3>
        <div class="mt-4 w-full rounded-lg bg-white px-4">
          <h3 class="pt-6 text-lg font-bold xl:pl-6">
            Taux d’utilisation des accords
          </h3>
          <div class="flex flex-col items-center justify-center">
            <div class="flex items-center justify-center gap-8">
              <div class="w-[170px] sm:w-[250px] lg:w-[350px]">
                <VueUiDonut
                  :config="usedAccordsGraphConfig()"
                  :dataset="dataset"
                />
              </div>
              <div class="flex flex-col gap-2">
                <div
                  v-for="datapoint in dataset"
                  :key="datapoint.name"
                  class="flex flex-row items-center gap-2"
                >
                  <div
                    :style="{
                      backgroundColor: datapoint.color,
                      height: '20px',
                      width: '20px',
                    }"
                  />
                  <span class="text-sm md:text-base"
                    >{{ datapoint.name }} ({{
                      calculatePercent(datapoint.values[0], availableAccords)
                    }}%)</span
                  >
                </div>
              </div>
            </div>
            <div class="mb-2 text-center text-base md:text-lg">
              Vous utilisez actuellement
              <span class="text-secondary">{{ percentUsedAccords }}%</span> des
              accords disponibles
            </div>
            <RouterLink
              :to="{
                name: ProductPageList.PRODUCTS,
              }"
              class="text- mb-4 text-secondary underline"
            >
              Voir tous les accords disponibles
            </RouterLink>
          </div>
        </div>
      </div>
    </template>
  </AccountPage>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { VueUiDonut } from 'vue-data-ui'

import { useUserStore } from '@/vuejs/stores/user.ts'
import { ProductPageList } from '@/vuejs/router/pages-list.ts'
import { usedAccordsGraphConfig } from '@/vuejs/modules/account/utils/graph_usedAccords.ts'

import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { useChannelStore } from '@/vuejs/stores/channel.ts'
import { storeToRefs } from 'pinia'

const userStore = useUserStore()

const { channelPrimaryColor, channelSecondaryColor } =
  storeToRefs(useChannelStore())

const adherent = userStore.user.account.adherent

const usedAccords = computed((): number => {
  return adherent.usedAccords
})

const availableAccords = computed((): number => {
  return adherent.availableAccords
})

const noUsedAccords = computed((): number => {
  return adherent.availableAccords - adherent.usedAccords
})

const percentUsedAccords = computed((): number => {
  return Math.round((usedAccords.value / adherent.availableAccords) * 100)
})

const dataset = [
  {
    name: 'Accords utilisés',
    values: [usedAccords.value],
    color: channelSecondaryColor,
  },
  {
    name: 'Accords non utilisés',
    values: [noUsedAccords.value],
    color: channelPrimaryColor,
  },
]

const calculatePercent = (v1: number, v2: number): number => {
  return Math.round((v1 * 100) / v2)
}
</script>
