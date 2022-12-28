<template>
  <RouterView v-if="props.component === ''" />
  <LoginForm v-else-if="props.component === 'login'" />
  <PartnersCarousel v-else-if="props.component === 'partners-carousel'" />
  <StickyContactButtons
    v-else-if="props.component === 'sticky-contact-buttons'"
  />
</template>

<script lang="ts" setup>
import { onMounted } from 'vue'
import LoginForm from './modules/login/views/ExternalLoginForm.vue'
import PartnersCarousel from '@/vuejs/modules/shared/PartnersCarouselComponent.vue'
import StickyContactButtons from '@/vuejs/modules/shared/StickyContactButtonsComponent.vue'
import {useCompanyStore} from '@/vuejs/stores/company'
const companyStore = useCompanyStore()
const props = defineProps({
  component: {
    required: false,
    type: String,
    default: '',
  },
})

onMounted(async () => {
  await companyStore.getAdresses()
})
</script>

<style lang="postcss">
body {
  font-family: 'CoText';
  background: #f2f0f6;
}
</style>
