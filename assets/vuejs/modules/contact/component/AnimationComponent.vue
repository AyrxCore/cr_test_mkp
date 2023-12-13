<template>
  <div class="mt-10 justify-center">
    <h3 class="home-title mb-6 text-primary">
      Besoin de contacter votre animateur ?
    </h3>
    <div class="mt-5 flex flex-col lg:flex-row">
      <div class="w-full lg:w-2/5">
        <div
          class="flex h-auto w-[100%] flex-col rounded-lg bg-white p-3 lg:h-[191px] lg:flex-row lg:p-7"
        >
          <div class="m-auto flex rounded-full border lg:w-[25%]">
            <img
              :src="helpImageFile"
              alt="animateur"
              class="h-[121px] items-center sm:mx-auto"
            />
          </div>
          <div
            class="flex flex-col items-center justify-start px-2 lg:w-[80%] lg:pl-5"
          >
            <h3
              class="flex w-full justify-center text-[19px] lg:justify-start lg:text-[25px]"
            >
              <span class="text-gradient">SERVICE ADHERENT</span>
            </h3>
            <p
              class="mt-3 flex flex-col justify-center text-sm md:text-base lg:mt-5 lg:flex-row lg:text-lg"
            >
              <PhoneIconComponent
                :stroke-color="channel.design.primaryColor"
                class="w-full lg:mx-auto lg:mr-2 lg:w-auto"
              />
              <span class="text-primary underline lg:w-[313px]">
                <a :href="`tel:${channel.phoneNumber}`">Appelez</a>
              </span>
            </p>
            <p
              class="mt-5 flex flex-col text-sm md:text-base lg:mt-0 lg:flex-row lg:text-lg"
            >
              <MailIconComponent
                class="w-full text-primary lg:mx-auto lg:mr-2 lg:w-auto"
              />
              <span class="text-primary underline lg:w-[313px]">
                <a :href="`mailto:${channel.email}`">Envoyer un email</a>
              </span>
            </p>
          </div>
        </div>
      </div>
      <div
        class="mt-3 h-[380px] w-full rounded-lg bg-white lg:ml-14 lg:mt-0 lg:w-3/5"
      >
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
import helpImage from '@/vuejs/assets/img/samples/img-help-product.png'
import { storeToRefs } from 'pinia'
import { useChannelStore } from '@/vuejs/stores/channel'

const helpImageFile = getImage(helpImage)
const defaultImageFile = getImage(defaultImage)

const { channel } = storeToRefs(useChannelStore())
</script>

<style scoped></style>
