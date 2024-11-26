<template>
  <BaseTemplate :title="accordTitle">
    <LoadingComponent v-if="isLoading" />
    <div v-else-if="accord && !isLoading && !isInShowcase" class="m-auto my-4">
      <HeaderPartnerComponent
        :name="accord.name"
        :note="accord.properties.note_rse ?? null"
        :banner-desktop="accord.properties.banniere_partenaire"
        :banner-text="accord.properties.texte_banniere"
        :categories="accord.categories"
        @scroll-to="scrollTo('#sectionRse')"
      />

      <div class="mt-12 flex flex-col text-sm md:text-base lg:text-lg">
        <ConditionsNegocieesComponent
          :properties="accord.properties"
          :accord-name="accord.name"
        />

        <div class="mt-5 flex flex-col items-center bg-primary p-6 lg:mt-0">
          <h3
            class="text-title-default-size mb-6 mt-5 text-center font-bold text-white"
          >
            Comment bénéficier des conditions négociées&nbsp;?
          </h3>
          <ConditionsNotActivatedComponent
            v-if="
              status.not_activated === currentStatus.status &&
              !accord.properties.process_fat_client?.length
            "
            :label="accord.properties.cta_text_not_activated"
            :text="
              currentChannel.code === 'QANTIS_ACHAT'
                ? accord.properties.process_not_activated
                : accord.properties.process_not_activated_mb
            "
            :current-status="currentStatus"
            :accord-name="accord.name"
          />
          <ConditionsClientComponent
            v-else-if="
              status.not_activated === currentStatus.status &&
              !!accord.properties.process_fat_client?.length
            "
            :properties="accord.properties"
            :accord-name="accord.name"
          />
          <ConditionsPendingOrActivated
            v-else-if="status.not_activated !== currentStatus.status"
            :current-status="currentStatus"
            :properties="accord.properties"
            :accord-name="accord.name"
          />
        </div>
      </div>
      <div v-if="partnerProducts.length > 0" class="m-auto max-w-screen-94">
        <div class="mt-10 sm:w-[45rem]">
          <h3 class="text-title-primary">
            Sélection de produits du partenaire
          </h3>
        </div>
        <div class="m-auto max-w-screen-94">
          <ProductsCarouselComponent
            :loading="isLoading"
            :products="partnerProducts"
            @click-left="sendGaEvent('click_fat_slider_left')"
            @click-right="sendGaEvent('click_fat_slider_right')"
            @click-add-cart="
              sendGaEvent('click_fat_slider_product_add_cart', $event)
            "
            @click-title="sendGaEvent('click_fat_slider_product_title', $event)"
            @click-img="sendGaEvent('click_fat_slider_product_img', $event)"
            @click-moins-qty="
              sendGaEvent('click_fat_slider_product_moins_qty', $event)
            "
            @click-plus-qty="
              sendGaEvent('click_fat_slider_product_plus_qty', $event)
            "
          />
        </div>
      </div>
      <div class="mx-auto my-8 max-w-screen-2xl md:px-5">
        <PromotionnalComponent :properties="accord.properties" />
      </div>
      <EnSavoirPlusComponent
        :properties="accord.properties"
        :accord-name="accord.name"
      />
      <div id="sectionRse" class="scroll-mt-40" />
      <RseEngagementComponent :properties="accord.properties" />
      <div class="mb-12 mt-8 px-6 lg:px-12">
        <h3 class="text-title-primary">
          Ces partenaires peuvent aussi vous intéresser
        </h3>
        <PartnersCarouselComponent
          class="mt-5"
          :params="sellersByCategoryParam"
          @click-partner-slider="
            sendGaEvent('click_fat_frise_logos', {
              partenaire_name: $event,
            })
          "
        />
      </div>
    </div>
    <div
      v-else
      class="xs:w-[100%] m-auto my-4 flex max-w-screen-2xl justify-center px-5 sm:px-8"
    >
      Aucun accord cadre n'a été trouvé avec cette référence
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import HeaderPartnerComponent from '@/vuejs/modules/products/components/accord-cadre/HeaderAccordCadreComponent.vue'
import { computed, ref, watch } from 'vue'
import PartnersCarouselComponent from '@/vuejs/modules/shared/PartnersCarouselComponent.vue'
import { Product } from '@/vuejs/types/Product'
import { useRoute, useRouter } from 'vue-router'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import ConditionsNegocieesComponent from '@/vuejs/modules/products/components/accord-cadre/ConditionsNegocieesComponent.vue'
import { status } from '@/vuejs/modules/products'
import ConditionsNotActivatedComponent from '@/vuejs/modules/products/components/accord-cadre/ConditionsNotActivatedComponent.vue'
import ConditionsPendingOrActivated from '@/vuejs/modules/products/components/accord-cadre/ConditionsPendingOrActivatedComponent.vue'
import ConditionsClientComponent from '@/vuejs/modules/products/components/accord-cadre/ConditionsClientComponent.vue'
import EnSavoirPlusComponent from '@/vuejs/modules/products/components/accord-cadre/EnSavoirPlusComponent.vue'
import { useProductStore } from '@/vuejs/stores/product'

import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useUserStore } from '@/vuejs/stores/user'

import PromotionnalComponent from '@/vuejs/modules/products/components/accord-cadre/PromotionnalComponent.vue'
import RseEngagementComponent from '@/vuejs/modules/products/components/accord-cadre/RseEngagementComponent.vue'
import { MainPageList } from '@/vuejs/router/pages-list'
import { storeToRefs } from 'pinia'

const route = useRoute()
const router = useRouter()
const accordStore = useProductStore()
const channelStore = useChannelStore()
const productStore = useProductStore()
const accord = ref<Product>()
const isLoading = ref<boolean>(false)
const partnerProducts = ref<Product[]>()
const { adherentTarifShowcases } = storeToRefs(useUserStore())

const currentChannel = channelStore.currentChannel

const sellersByCategoryParam = computed(() => {
  return {
    categories: [accord.value.categories[0].id],
  }
})

const currentStatus = computed(() => {
  return accord.value.accountAccordCadre
})

const scrollTo = (selector) => {
  const element = document.querySelector(selector)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

const accordTitle = computed(() => {
  return accord.value ? accord.value.name + ' | ' : ''
})

const isInShowcase = computed<boolean>(() =>
  accord.value && accord.value.properties
    ? adherentTarifShowcases.value.some(
        (showcase) =>
          showcase.accordId === accord.value!.properties['accord-id'],
      )
    : false,
)

watch(
  () => isInShowcase.value,
  (newValue) => {
    if (newValue) {
      router.push({ name: MainPageList.HOME_PAGE })
    }
  },
  { immediate: true },
)

watch(
  () => route.params.slug as string,
  async (slug: string) => {
    isLoading.value = true
    try {
      if (slug) {
        const accordId = slug.split('-')
        accord.value = await accordStore.findAccordCadreById(
          accordId[accordId.length - 1],
        )
        if (!isInShowcase.value) {
          partnerProducts.value = await productStore.findPartnerProducts(
            accord.value.seller.id,
          )
        }
      }
    } catch (error) {
      console.error(error)
    } finally {
      isLoading.value = false
    }
  },
  { immediate: true },
)
</script>

<style scoped>
.bloc-content {
  @apply rounded-lg bg-white p-4 text-gray-500 md:p-7.5;
}

.condition-beneficiaire p {
  @apply mb-4 text-sm md:text-base xl:text-lg;
}
</style>
