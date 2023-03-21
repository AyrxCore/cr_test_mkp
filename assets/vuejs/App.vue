<template>
  <RouterView v-if="props.component === ''" />
  <LoginForm v-else-if="props.component === 'login'" />
  <PartnersCarousel v-else-if="props.component === 'partners-carousel'" />
  <ArrowRightIconComponent v-else-if="props.component === 'arrow-right-icon'" />
  <ArrowLeftIconComponent v-else-if="props.component === 'arrow-left-icon'" />
  <FormComponent v-else-if="props.component === 'contact-form'" />
  <StickyContactButtons
    v-else-if="props.component === 'sticky-contact-buttons'"
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
import { useCategoryStore } from '@/vuejs/stores/category'
import { useBuyerCompanyStore } from '@/vuejs/stores/buyer_company'
import { useCartStore } from '@/vuejs/stores/cart'
import router from './router'
import { CartPageList } from './router/pages-list'

const cartStore = useCartStore()
const companyStore = useBuyerCompanyStore()
const categoryStore = useCategoryStore()

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
      categoryStore.initAllCategories(),
    ])
  }
})

onMounted(async () => {
  await router.isReady()
  if (
    props.component === '' &&
    router.currentRoute.value.name !== CartPageList.RECAP
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
