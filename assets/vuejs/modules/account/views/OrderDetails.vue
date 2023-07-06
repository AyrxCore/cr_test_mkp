<template>
  <AccountPage>
    <template #right-side>
      <div
        v-if="isLoading"
        class="mt-5 flex h-20 w-full items-center justify-center"
      >
        <LoaderSharedComponent
          class="text-secondary"
          classes="loader-xl loader"
        />
      </div>
      <div v-else-if="showAlert" class="lg:w-5/6">
        <AlertSharedComponent
          class="bg-red-200 text-red-800 dark:bg-red-200 dark:text-red-800"
        />
        <RouterLink :to="{ name: AccountPageList.ORDERS }">
          <span class="button button-white button-white-secondary">
            <ArrowLeftIconComponent />
            Retour à la liste
          </span>
        </RouterLink>
      </div>
      <div v-else>
        <h3 class="primary my-2 text-title-35 lg:mb-2">
          Commande {{ order.orderNumber }}
        </h3>
        <p class="text-gray-500">
          Ci-dessous, vous trouverez des détails sur les articles que vous avez
          commandés. Si vous avez commandé plus d’un article, veuillez noter que
          certains articles peuvent afficher une méthode d’expédition différente
          et/ou un état différent parce qu’ils sont expédiés dans un emballage
          distinct.
        </p>
        <div class="mt-5 flex w-full rounded-lg bg-white p-5">
          <div
            class="mr-5 flex flex-col items-end justify-center space-y-4 text-gray-500"
          >
            <div>Numéro de la commande:</div>
            <div>Date de la commande:</div>
            <div>Etat de la commande:</div>
            <div>Total de la commande HT :</div>
            <div>Total de la commande TTC:</div>
          </div>
          <div
            class="flex flex-col items-center justify-center space-y-4 text-gray-500"
          >
            <div>{{ order.orderNumber }}</div>
            <div>{{ formatDateFr(order.createdAt) }}</div>
            <div
              class="mt-1 w-fit rounded-md px-1 text-[14px] text-white"
              :class="ORDER_STATUS[order.state].color"
              :title="ORDER_STATUS[order.state].name"
            >
              {{ ORDER_STATUS[order.state].name }}
            </div>
            <div>
              <span class="mr-2 flex font-bold text-primary md:mr-0"
                >{{ formatPrice(order.totalExcludingTaxes) }} € HT</span
              >
            </div>
            <div>
              <span class="flex text-gray-500"
                >({{ formatPrice(order.total) }} € TTC)</span
              >
            </div>
          </div>
        </div>

        <div class="mt-5 flex w-full">
          <div
            class="mr-5 flex w-1/2 flex-col items-start justify-center space-y-4 rounded-lg bg-white p-5 text-gray-500"
          >
            <h3>Adresse de livraison</h3>
            <div>{{ order.shippingAddress }}</div>
            <div class="flex items-center">
              Etat de la livraison:
              <div
                class="ml-2 mt-1 w-fit rounded-md px-1 text-[14px] text-white"
                :class="SHIPPING_STATUS[order.shippingState].color"
                :title="SHIPPING_STATUS[order.shippingState].name"
              >
                {{ SHIPPING_STATUS[order.shippingState].name }}
              </div>
            </div>
          </div>
          <div
            class="flex w-1/2 flex-col items-start justify-center space-y-4 rounded-lg bg-white p-5 text-gray-500"
          >
            <h3>Adresse de facturation</h3>
            <div>{{ order.billingAddress }}</div>
          </div>
        </div>
        <div class="mt-5 flex w-full flex-col">
          <div class="flex p-2 text-gray-500">
            <div class="flex md:w-8/12 lg:w-9/12">
              <span>Description d'article</span>
            </div>
            <div class="flex justify-between md:w-4/12 lg:w-3/12">
              <span>Prix unitaire</span>
              <span>Sous total</span>
            </div>
          </div>

          <OrderDetailsComponent
            v-for="(item, key) in order.items"
            :key="key"
            :item="item"
          />
        </div>
        <div class="mt-5 flex w-full justify-end">
          <div class="flex flex-col">
            <h4 class="text-gray-500">Récapitulatif</h4>
            <div class="flex rounded-lg bg-white px-2 py-3">
              <div
                class="mr-2 flex flex-col items-end justify-center space-y-3 text-sm text-gray-500"
              >
                <div>Sous-total HT:</div>
                <div>Faris de livraison HT:</div>
                <div>Total TTC:</div>
              </div>
              <div
                class="flex flex-col items-start justify-center space-y-3 text-sm text-gray-500"
              >
                <div>{{ formatPrice(order.totalExcludingTaxes) }} € HT</div>
                <div>{{ formatPrice(order.shipmentAmount) }} € HT</div>
                <div>{{ formatPrice(order.total) }} € TTC</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { ref, watch } from 'vue'
import { useOrderStore } from '@/vuejs/stores/order'
import { useRoute } from 'vue-router'
import { Order } from '@/vuejs/types/Order'
import { formatDateFr, formatPrice } from '@/vuejs/services/utils'
import { ORDER_STATUS, SHIPPING_STATUS } from '@/vuejs/services/const'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { storeToRefs } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import { AccountPageList } from '@/vuejs/router/pages-list'
import ArrowLeftIconComponent from '@/vuejs/App.vue'
import OrderDetailsComponent from '@/vuejs/modules/account/components/OrderDetailsComponent.vue'

const route = useRoute()
const orderStore = useOrderStore()
const order = ref<Order>()
const isLoading = ref<boolean>(false)
const alertStore = useAlertStore()
const { show: showAlert } = storeToRefs(alertStore)

watch(
  () => route.params.id as string,
  async (id: string) => {
    isLoading.value = true
    try {
      if (id) {
        order.value = await orderStore.getOrderById(parseInt(id))
      }
    } catch (error) {
    } finally {
      isLoading.value = false
    }
  },

  { immediate: true },
)
</script>

<style scoped></style>
