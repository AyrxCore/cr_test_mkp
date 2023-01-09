<template>
  <div class="flex flex-col">
    <div class=" sm:-mx-6 lg:-mx-8">
      <div class="inline-block min-w-full sm:px-6 lg:px-8">
        <div class="overflow-hidden">
          <img
            :src="fatLandscapeImg"
            alt="Picture"
            class="items-center rounded-lg sm:mx-auto"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { getImage } from '@/vuejs/services/utils'
import fatLandscape from '@/vuejs/assets/img/samples/img-fat-landscape.png'
import fatPortrait from '@/vuejs/assets/img/samples/img-fat-portrait.png'
const fatLandscapeImg = getImage(fatLandscape)
const fatPortraitImg = getImage(fatPortrait)

</script>

<style scoped></style>
