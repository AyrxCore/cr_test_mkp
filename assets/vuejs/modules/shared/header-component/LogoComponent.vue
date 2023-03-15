<template>
  <div class="flex justify-center items-center lg:min-w-[200px]">
    <RouterLink
      :to="{ name: PageList.HOME_PAGE }"
      class="text-xl font-bold text-white"
    >
      <img
        :src="qantisLogoImg"
        alt="Logo Qantis"
        class="left-[60px] top-[24.5px] flex h-auto w-[123px] md:w-[145px]"
      />
    </RouterLink>
  </div>
</template>

<script lang="ts" setup>
import { getImage } from '@/vuejs/services/utils'
import qantisHeaderLogo from '@/vuejs/assets/img/qantis_marketplace_logo_blanc.png'
import { PageList } from '@/vuejs/router'

const qantisLogoImg = getImage(qantisHeaderLogo)
</script>
