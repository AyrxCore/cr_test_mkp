<template>
  <div class="mt-1 flex">
    <a href="/app/home" class="text-xl font-bold text-white">
      <img
        :src="qantisLogoImg"
        alt="Logo Qantis"
        class="flex logo-qantis-header"
      />
    </a>
  </div>
</template>

<script lang="ts" setup>
import { getImage } from '@/vuejs/services/utils'
import qantisHeaderLogo from '@/vuejs/assets/img/qantis_marketplace_logo_blanc.png'

const qantisLogoImg = getImage(qantisHeaderLogo)
</script>


<style lang="scss">
@import 'assets/style/_variables.scss';

.logo-qantis-header {
  @apply h-auto w-[145px] left-[60px] top-[24.5px];
}
</style>

