<template>
  <template v-if="props.component === ''">
    <RouterView />
    <NotifComponent />
  </template>
  <LoginForm v-else-if="props.component === 'login'" />
  <PrehomeRightPart v-else-if="props.component === 'prehome-right-part'" />
  <FooterPrehome v-else-if="props.component === 'footer-prehome'" />
  <ArrowRightIconComponent v-else-if="props.component === 'arrow-right-icon'" />
  <ArrowLeftIconComponent v-else-if="props.component === 'arrow-left-icon'" />
  <FormComponent v-else-if="props.component === 'contact-form'" />
  <StickyContactButtons
    v-else-if="props.component === 'sticky-contact-buttons'"
  />
  <CmsPageComponent
    v-else-if="props.component === 'mentions-legales' && currentChannel"
    :page-id="channelDocuments?.legalTerms"
  />
  <CmsPageComponent
    v-else-if="
      props.component === 'politique-de-confidentialite' && currentChannel
    "
    :page-id="channelDocuments?.privacyPolicy"
  />
  <CmsPageComponent
    v-else-if="
      props.component === 'conditions-generales-d-utilisation' && currentChannel
    "
    :page-id="channelDocuments?.generalTermsOfUse"
  />
</template>

<script lang="ts" setup>
import { onBeforeMount, onMounted } from 'vue'

import LoginForm from './modules/login/views/ExternalLoginForm.vue'
import StickyContactButtons from '@/vuejs/modules/shared/StickyContactButtonsComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ArrowLeftIconComponent from '@/vuejs/modules/shared/icon/ArrowLeftIconComponent.vue'
import FormComponent from '@/vuejs/modules/contact/component/FormComponent.vue'
import NotifComponent from '@/vuejs/modules/shared/NotifComponent.vue'
import CmsPageComponent from '@/vuejs/modules/shared/CmsPageComponent.vue'

import { useCategoryStore } from '@/vuejs/stores/category'
import { useCartStore } from '@/vuejs/stores/cart'
import { useChannelStore } from '@/vuejs/stores/channel'
import router from './router'
import { CartPageList } from './router/pages-list'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'
import { useBannerStore } from '@/vuejs/stores/banner'
import PrehomeRightPart from '@/vuejs/modules/login/component/PrehomeRightPart.vue'
import FooterPrehome from '@/vuejs/modules/login/component/FooterPrehome.vue'
import { storeToRefs } from 'pinia'

const channelStore = useChannelStore()
const { currentChannel, channelDocuments } = storeToRefs(channelStore)
const cartStore = useCartStore()
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
  // The channel must be the first thing to fetch because we need to send every other requests with the "X-Channel" header
  await channelStore.getChannel(window.location.hostname)
  if (props.component === '') {
    const promises = []
    promises.push(categoryStore.getAllCategories())
    if (
      channelStore.isAllowedToShow(OPTIONAL_FRONT_BLOCKS.BANNER_FLASH_HOMEPAGE)
    ) {
      promises.push(bannerStore.init())
    }
    await Promise.all(promises)
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
  font-family: 'Roboto';
  background: var(--body-background);
  //color: var(--default-text-color);
}

h3 {
  font-family: 'CoText';
}
</style>
