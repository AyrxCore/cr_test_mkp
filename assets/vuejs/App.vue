<template>
  <template v-if="props.component === ''">
    <RouterView />
    <NotifComponent />
    <MarketplaceVersion />
    <InternalStellantisModal
      v-if="!userStore.isNeoAutoLogin && !userStore.isShouldHideStellantisModal"
    />
  </template>
  <LoginForm v-else-if="props.component === 'login'" />
  <PrehomeRightPart v-else-if="props.component === 'prehome-right-part'" />
  <FooterPrehome v-else-if="props.component === 'footer-prehome'" />
  <ArrowRightIconComponent v-else-if="props.component === 'arrow-right-icon'" />
  <ArrowLeftIconComponent v-else-if="props.component === 'arrow-left-icon'" />
  <FormComponent v-else-if="props.component === 'contact-form'" />
  <CmsPageComponent
    v-else-if="props.component === 'mentions-legales' && currentChannel"
    field="legalTerms"
  />
  <CmsPageComponent
    v-else-if="
      props.component === 'politique-de-confidentialite' && currentChannel
    "
    field="privacyPolicy"
  />
  <CmsPageComponent
    v-else-if="
      props.component === 'conditions-generales-d-utilisation' && currentChannel
    "
    field="cgu"
  />
</template>

<script lang="ts" setup>
import { onBeforeMount, onMounted } from 'vue'
import { storeToRefs } from 'pinia'

import router from '@/vuejs/router'
import { CartPageList } from '@/vuejs/router/pages-list'
import { useBannerStore } from '@/vuejs/stores/banner'
import { useCartStore } from '@/vuejs/stores/cart'
import { useCategoryStore } from '@/vuejs/stores/category'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useUserStore } from '@/vuejs/stores/user'
// TODO (MKP-1411): Décommenter quand les Favoris seront disponibles via DJUST
// import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { useNewsStore } from '@/vuejs/stores/news'
import { useSellerStore } from '@/vuejs/stores/seller'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'

import LoginForm from '@/vuejs/modules/login/views/ExternalLoginForm.vue'
import FormComponent from '@/vuejs/modules/contact/component/FormComponent.vue'
import NotifComponent from '@/vuejs/modules/shared/NotifComponent.vue'
import MarketplaceVersion from '@/vuejs/modules/shared/MarketplaceVersion.vue'
import CmsPageComponent from '@/vuejs/modules/shared/CmsPageComponent.vue'
import PrehomeRightPart from '@/vuejs/modules/login/component/PrehomeRightPart.vue'
import FooterPrehome from '@/vuejs/modules/login/component/FooterPrehome.vue'
import InternalStellantisModal from '@/vuejs/modules/shared/InternalStellantisModal.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ArrowLeftIconComponent from '@/vuejs/modules/shared/icon/ArrowLeftIconComponent.vue'

const channelStore = useChannelStore()
const cartStore = useCartStore()
const categoryStore = useCategoryStore()
const bannerStore = useBannerStore()
const userStore = useUserStore()
// TODO (MKP-1411): Décommenter quand les Favoris seront disponibles via DJUST
// const favoriteStore = useFavoriteStore()
const newsStore = useNewsStore()
const sellerStore = useSellerStore()

const { currentChannel } = storeToRefs(channelStore)

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
    promises.push(newsStore.initialize())
    promises.push(sellerStore.getAllSellers())
    // if (
    //   channelStore.isAllowedToShow(OPTIONAL_FRONT_BLOCKS.BANNER_FLASH_HOMEPAGE)
    // ) {
    //   promises.push(bannerStore.init())
    // }
    // TODO (MKP-1411): Appel temporairement désactivé - à rétablir quand les Favoris seront disponibles via DJUST
    // if (channelStore.isAllowedToShow(OPTIONAL_FRONT_BLOCKS.FAVORITES)) {
    //   promises.push(favoriteStore.fetchFavorites())
    // }
    await Promise.all(promises)
  }
})

onMounted(async () => {
  await router.isReady()
  if (
    props.component === '' &&
    ![
      CartPageList.CART_RECAP,
      CartPageList.CART_PAYMENT,
      CartPageList.CART_PAYMENT_SEPA,
      CartPageList.CART_PAYMENT_ERROR,
      CartPageList.CART_CONFIRMED,
    ].includes(router.currentRoute.value.name as CartPageList)
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
