<template>
  <template v-if="props.component === ''">
    <RouterView />
    <NotifComponent />
  </template>
  <LoginForm v-else-if="props.component === 'login'" />
  <PartnersCarousel v-else-if="props.component === 'partners-carousel'" />
  <ArrowRightIconComponent v-else-if="props.component === 'arrow-right-icon'" />
  <ArrowLeftIconComponent v-else-if="props.component === 'arrow-left-icon'" />
  <FormComponent v-else-if="props.component === 'contact-form'" />
  <StickyContactButtons
    v-else-if="props.component === 'sticky-contact-buttons'"
  />
  <CmsPageComponent
    v-else-if="props.component === 'mentions-legales'"
    :page-id="MENTIONS_LEGALES_PAGE_ID"
  />
  <CmsPageComponent
    v-else-if="props.component === 'politique-de-confidentialite'"
    :page-id="POLITIQUE_DE_CONFIDENTIALITE_PAGE_ID"
  />
</template>

<script lang="ts" setup>
import { onBeforeMount, onMounted } from 'vue'

import LoginForm from './modules/login/views/ExternalLoginForm.vue'
import PartnersCarousel from '@/vuejs/modules/shared/PartnersCarouselComponent.vue'
import StickyContactButtons from '@/vuejs/modules/shared/StickyContactButtonsComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ArrowLeftIconComponent from '@/vuejs/modules/shared/icon/ArrowLeftIconComponent.vue'
import FormComponent from '@/vuejs/modules/contact/component/FormComponent.vue'
import NotifComponent from '@/vuejs/modules/shared/NotifComponent.vue'
import CmsPageComponent from '@/vuejs/modules/shared/CmsPageComponent.vue'

import { useCategoryStore } from '@/vuejs/stores/category'
import { useAddressStore } from '@/vuejs/stores/address'
import { useCartStore } from '@/vuejs/stores/cart'
import router from './router'
import { CartPageList } from './router/pages-list'
import {
  MENTIONS_LEGALES_PAGE_ID,
  POLITIQUE_DE_CONFIDENTIALITE_PAGE_ID,
} from '@/vuejs/services/const'
import { useBannerStore } from '@/vuejs/stores/banner'

const cartStore = useCartStore()
const companyStore = useAddressStore()
const categoryStore = useCategoryStore()
const bannerStore = useBannerStore()

const props = defineProps({
  component: {
    required: false,
    type: String,
    default: '',
  },
})

onBeforeMount(async () => {
  if (props.component === '') {
    await Promise.all([
      companyStore.getAddresses(),
      categoryStore.init(),
      bannerStore.init(),
    ])
  }
})

onMounted(async () => {
  await router.isReady()
  if (
    props.component === '' &&
    ![CartPageList.CART_RECAP, CartPageList.CART_PAYMENT_ERROR].includes(
      router.currentRoute.value.name,
    )
  ) {
    await cartStore.getCart()
  }
})
</script>

<style lang="postcss">
body {
  font-family: 'CoText';
  background: #f2f0f6;
}
</style>
