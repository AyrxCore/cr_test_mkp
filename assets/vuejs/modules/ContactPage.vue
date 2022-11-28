<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div
      class="xs:w-[100%] text-cotext m-auto my-4 max-w-screen-2xl flex-1 sm:px-8"
    >
      <breadcrumb-shared-component :current-page="'Contact'" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <h3 class="primary text-[35px]">Contactez-nous</h3>
      <span class="mb-5 text-lg text-gray-500"
        >Notre Service adhérent est disponible du lundi au vendredi de 8h30 à
        18h</span
      >
      <div
        class="text-cotext m-auto my-2 mt-14 grid w-[100%] max-w-screen-2xl grid-cols-3 gap-4"
      >
        <!-- Bloc text actualité -->
        <div class="grid grid-rows-2 border-r-2 border-gray-300 pr-[5.5rem]">
          <div class="mb-5 h-[180px] rounded-lg bg-white py-8 text-center">
            <PhoneIconComponent
              :stroke-color="'#9553FF'"
              class="mx-auto mb-2 w-full"
            />
            <h4 class="primary mb-2 font-bold">Par téléphone</h4>
            <span class="w-[313px] text-lg text-gray-500">
              Appelez-nous au 04 37 65 06 21 <br />
              du lundi au vendredi de 8h30 à 18h
            </span>
          </div>
          <div class="h-[180px] rounded-lg bg-white py-8 text-center">
            <MailIconComponent
              :stroke-color="'#9553FF'"
              class="mx-auto mb-2 w-full"
            />
            <h4 class="primary mb-2 font-bold">Par email</h4>
            <span class="w-[313px] text-lg text-gray-500">
              à <a href="mailto:animation@qantis.co">animation@qantis.co</a>
            </span>
          </div>
        </div>
        <!-- Fin Bloc text actualité -->

        <!-- Bloc formulaire -->
        <div class="right-0 col-span-2 rounded-lg pl-[5.5rem] text-center">
          <MessageSquareIconComponent
            :stroke-color="'#9553FF'"
            class="mx-auto mb-2 w-full"
          />
          <h4 class="primary mb-3 font-bold">
            Directement en nous laissant un message
          </h4>
          <div class="app-advanced">
            <form method="POST" target="_blank" class="mx-auto w-full">
              <div class="mb-3 pt-0">
                <select
                  class="relative h-[55px] w-full rounded-md border-0 text-gray-600 placeholder-gray-400"
                >
                  <option>Votre demande concerne</option>
                </select>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div class="h-[255px]">
                  <div class="mb-3 pt-0">
                    <input
                      type="text"
                      placeholder="Votre nom"
                      name="name"
                      class="relative h-[55px] w-full rounded-lg border-0 bg-white px-3 text-gray-600 placeholder-gray-400"
                      required
                    />
                  </div>
                  <div class="mb-3 pt-0">
                    <input
                      type="text"
                      placeholder="Votre prénom"
                      name="name"
                      class="relative h-[55px] w-full rounded-lg border-0 bg-white px-3 text-gray-600 placeholder-gray-400"
                      required
                    />
                  </div>

                  <div class="mb-3 pt-0">
                    <input
                      type="email"
                      placeholder="Votre email"
                      name="email"
                      class="relative h-[55px] w-full rounded-lg border-0 bg-white px-3 text-gray-600 placeholder-gray-400"
                      required
                    />
                  </div>
                  <div class="mb-3 pt-0">
                    <input
                      type="text"
                      placeholder="Votre téléphone (Optionnel)"
                      name="name"
                      class="relative h-[55px] w-full rounded-lg border-0 bg-white px-3 text-gray-600 placeholder-gray-400"
                    />
                  </div>
                </div>
                <div class="h-[255px]">
                  <div class="pt-0">
                    <textarea
                      placeholder="Votre message"
                      name="message"
                      class="relative h-[255px] w-full resize-none rounded-lg border-0 bg-white px-3 py-3 text-gray-600 placeholder-gray-400"
                      rows="7"
                      required
                    />
                  </div>
                </div>
              </div>
              <div class="float-right pt-0">
                <DefaultButtonComponent
                  type="submit"
                  class="mt-2 justify-center text-center"
                >
                  <ArrowRigntIconComponent
                    :stroke-color="'#FFFFFF'"
                    class="mr-2 w-4"
                  />
                  Envoyer
                </DefaultButtonComponent>
              </div>
            </form>
          </div>
        </div>
        <!-- Fin Bloc formulaire -->
      </div>

      <!-- Bloc articles recommandés -->
      <div class="mt-10 justify-center">
        <h3 class="primary home-subtitle mb-6">
          Besoin de contacter votre animateur ?
        </h3>
        <div class="mt-5 flex">
          <div class="w-[40%]">
            <div
              class="text-cotext text-cotext inline-flex h-[191px] w-[100%] rounded-lg bg-white p-7"
            >
              <div class="m-auto w-[25%] rounded-full border">
                <img
                  :src="defaultImageFile"
                  alt="animateur"
                  class="h-[121px] w-[121px] items-center sm:mx-auto"
                />
              </div>
              <div class="w-[80%] pl-5">
                <h3
                  class="bg-gradient-to-r from-purple-500 via-blue-500 to-cyan-500 bg-clip-text text-[25px] text-transparent"
                >
                  Nom de l'animateur
                </h3>
                <p class="mt-5 inline-flex">
                  <PhoneIconComponent
                    :stroke-color="'#050056'"
                    class="mx-auto mr-2"
                  />
                  <span class="primary w-[313px] text-lg underline">
                    Appelez Nom animateur
                  </span>
                </p>
                <p class="inline-flex text-lg">
                  <MailIconComponent
                    :stroke-color="'#050056'"
                    class="mx-auto mr-2"
                  />
                  <span class="primary w-[313px] underline">
                    <a href="mailto:animation@qantis.co"
                      >Envoyer un email à Nom animateur</a
                    >
                  </span>
                </p>
              </div>
            </div>
          </div>
          <div class="ml-14 h-[380px] w-[60%] rounded-lg bg-white">
            <img
              :src="defaultImageFile"
              alt="animateur"
              class="m-auto h-[inherit]"
            />
          </div>
        </div>
      </div>
      <!-- Fin Bloc articles recommandés -->
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import { getImage } from '@/vuejs/services/utils'
import defaultImage from '@/vuejs/assets/img/default-image.png'
import { computed, ref } from 'vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ArrowRigntIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import MessageSquareIconComponent from '@/vuejs/modules/shared/icon/MessageSquareIconComponent.vue'
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'

const defaultImageFile = getImage(defaultImage)
</script>

<style scoped></style>
