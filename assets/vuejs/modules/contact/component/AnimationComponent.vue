<template>
  <div class="mt-10 justify-center">
    <h3 class="text-primary home-subtitle mb-6">
      Besoin de contacter votre animateur ?
    </h3>
    <div class="mt-5 flex flex-col lg:flex-row">
      <div class="w-full lg:w-2/5">
        <div class="flex flex-col lg:flex-row h-auto lg:h-[191px] w-[100%] rounded-lg bg-white p-3 lg:p-7">
          <div class="flex m-auto lg:w-[25%] rounded-full border">
            <img
              :src="helpImageFile"
              alt="animateur"
              class="h-[121px] items-center sm:mx-auto"
            />
          </div>
          <div class="flex flex-col items-center justify-start lg:w-[80%] px-2 lg:pl-5">
            <h3
              class="text-[19px] lg:text-[25px] flex w-full justify-center lg:justify-start"
            >
              <span class="text-gradient">SERVICE ADHERENT</span>
            </h3>
            <p class="mt-3 lg:mt-5 flex flex-col lg:flex-row text-sm md:text-base lg:text-lg justify-center">
              <PhoneIconComponent
                :stroke-color="'#050056'"
                class="lg:mx-auto lg:mr-2 w-full lg:w-auto"
              />
              <span class="text-primary lg:w-[313px] underline">
                <a :href="`tel:${PHONE_ANIMATION}`">Appelez</a>
              </span>
            </p>
            <p class="flex flex-col lg:flex-row text-sm md:text-base lg:text-lg mt-5 lg:mt-0">
              <MailIconComponent
                class="lg:mx-auto lg:mr-2 w-full lg:w-auto text-primary"
              />
              <span class="text-primary lg:w-[313px] underline">
                <a :href="`mailto:${MAIL_ANIMATION}`">Envoyer un email</a>
              </span>
            </p>
          </div>
        </div>
      </div>
      <div class="lg:ml-14 h-[380px] w-full lg:w-3/5 rounded-lg bg-white mt-3 lg:mt-0">
        <img
          :src="defaultImageFile"
          alt="animateur"
          class="m-auto h-[inherit]"
        />
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { getImage } from '@/vuejs/services/utils'
import defaultImage from '@/vuejs/assets/img/default-image.png'
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import { MAIL_ANIMATION, PHONE_ANIMATION } from '@/vuejs/services/const'
import helpImage from '@/vuejs/assets/img/samples/img-help-product.png'

const helpImageFile = getImage(helpImage)
const defaultImageFile = getImage(defaultImage)
</script>

<style scoped></style>
