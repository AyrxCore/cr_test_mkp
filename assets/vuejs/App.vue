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
import { onBeforeMount } from 'vue'
import LoginForm from './modules/login/views/ExternalLoginForm.vue'
import PartnersCarousel from '@/vuejs/modules/shared/PartnersCarouselComponent.vue'
import StickyContactButtons from '@/vuejs/modules/shared/StickyContactButtonsComponent.vue'
import { useBuyerCompanyStore } from '@/vuejs/stores/buyer_company'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ArrowLeftIconComponent from '@/vuejs/modules/shared/icon/ArrowLeftIconComponent.vue'
import FormComponent from '@/vuejs/modules/contact/component/FormComponent.vue'
const companyStore = useBuyerCompanyStore()
const props = defineProps({
  component: {
    required: false,
    type: String,
    default: '',
  },
})

onBeforeMount(async () => {
  if (props.component === '') {
    await companyStore.getAdresses()
  }
})
</script>

<style lang="postcss">
body {
  font-family: 'CoText';
  background: #f2f0f6;
}
</style>
