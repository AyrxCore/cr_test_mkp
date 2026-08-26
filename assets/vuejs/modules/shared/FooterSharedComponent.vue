<template>
  <div
    :style="{
      color: betterTextColor('primary'),
    }"
    class="footer pb-4"
  >
    <div
      class="second-part grid grid-cols-1 gap-4 px-8 pb-5 pt-10 md:grid-cols-2 md:gap-8 md:px-16 xl:grid-cols-3 xl:gap-16"
    >
      <div class="order-1 mb-2 md:mb-4">
        <h3 class="mb-4 md:mb-6 lg:mb-7">
          {{ channel.name }}
        </h3>
        <div v-if="channel?.whiteLabel">
          {{ channel.options['TEXT_FOOTER'] }}
        </div>
        <div v-else>
          <div class="mb-4 flex">
            <QantisLogoComponent
              class="mt-[0.5rem] h-12 w-[168px] fill-primary stroke-white md:mt-0 md:h-16 md:w-[218px]"
            />
          </div>
          <div class="hidden xl:flex">
            <img
              :src="coqVertLogoImg"
              alt="header"
              class="mt-[0.5rem] h-16 w-[177px] object-contain"
            />
          </div>
        </div>
      </div>
      <div class="order-3 mb-2 md:order-2 md:mb-4">
        <h3 class="mb-4 md:mb-6 lg:mb-7">{{ memberArea }}</h3>
        <ul>
          <li class="mb-1">
            <RouterLink
              :to="{ name: AccountPageList.ACCOUNT }"
              @click="
                sendGtmEvent('footer_click', {
                  link_text: $event.target.innerText,
                  link_url: router.resolve({
                    name: AccountPageList.ACCOUNT,
                  }).fullPath,
                  origin_url: router.currentRoute.value.fullPath,
                })
              "
            >
              Mon compte
            </RouterLink>
          </li>
          <li class="mb-1">
            <RouterLink
              :to="{ name: AccountPageList.ORDERS }"
              @click="
                sendGtmEvent('footer_click', {
                  link_text: $event.target.innerText,
                  link_url: router.resolve({
                    name: AccountPageList.ORDERS,
                  }).fullPath,
                  origin_url: router.currentRoute.value.fullPath,
                })
              "
            >
              Mes commandes
            </RouterLink>
          </li>
          <!--
            TODO (MKP-1411): "Mes favoris" temporairement masqué dans le footer.
            À rétablir (décommenter) quand la fonctionnalité Favoris sera disponible via DJUST.
            <li class="mb-1">
              <RouterLink
                :to="{ name: AccountPageList.FAVORITES_LIST }"
                @click="
                  sendGtmEvent('footer_click', {
                    link_text: $event.target.innerText,
                    link_url: router.resolve({
                      name: AccountPageList.FAVORITES_LIST,
                    }).fullPath,
                    origin_url: router.currentRoute.value.fullPath,
                  })
                "
              >
                Mes favoris
              </RouterLink>
            </li>
          -->
          <!--
            TODO (MKP-1411): "Mes paniers sauvegardés" temporairement masqué dans le footer.
            À rétablir (décommenter) quand la fonctionnalité Paniers sauvegardés sera disponible via DJUST.
            <li class="mb-1">
              <RouterLink
                :to="{ name: AccountPageList.SAVED_CARTS }"
                @click="
                  sendGtmEvent('footer_click', {
                    link_text: $event.target.innerText,
                    link_url: router.resolve({
                      name: AccountPageList.SAVED_CARTS,
                    }).fullPath,
                    origin_url: router.currentRoute.value.fullPath,
                  })
                "
              >
                Mes paniers sauvegardés
              </RouterLink>
            </li>
          -->
        </ul>
      </div>
      <div class="order-2 mb-2 md:order-4 md:mb-4">
        <h3 class="mb-4 md:mb-6 lg:mb-7">Une question ?</h3>
        <ul>
          <li>
            <RouterLink
              :to="{ name: PageList.CONTACT_PAGE }"
              class="flex items-center border-none"
              @click="
                sendGtmEvent('contact_click', {
                  position: 'footer',
                  link_text: $event.target.innerText,
                  link_url: router.resolve({
                    name: PageList.CONTACT_PAGE,
                  }).fullPath,
                  origin_url: router.currentRoute.value.fullPath,
                })
              "
            >
              <MailIconComponent class="mr-4" fill="white" />
              Contactez-nous par message
            </RouterLink>
          </li>
          <li>
            <a
              :href="`tel:${channel?.phoneNumber}`"
              class="mt-4 flex hover:border-none"
            >
              <PhoneIconComponent class="mr-4" fill="white" />
              <div class="flex flex-col">
                <span>Par téléphone: {{ formattedPhoneNumber }}</span>
                <span>du lundi au vendredi, de 8h30 à 18h</span>
              </div>
            </a>
          </li>
        </ul>
      </div>
    </div>
    <hr class="mx-6 mb-4 md:mx-12" />
    <div class="justify-between px-8 pb-6 md:mx-16 md:flex md:px-0">
      <div
        class="flex w-full flex-col md:w-3/5 md:flex-row md:items-center md:justify-between"
      >
        <div class="mb-4 mr-4 md:mb-0">
          <a
            v-if="channelDocuments?.generalTermsOfUse"
            :href="channelGeneralTermsOfUseLink"
            target="_blank"
          >
            Conditions générales d'utilisation
          </a>
        </div>
        <div class="mb-4 mr-4 md:mb-0">
          <a
            v-if="channelDocuments?.legalTerms"
            :href="channelLegalTermsLink"
            target="_blank"
          >
            Mentions légales
          </a>
        </div>
        <div class="mb-4 mr-4 md:mb-0">
          <a
            v-if="channelDocuments?.privacyPolicy"
            :href="channelPrivacyPolicyLink"
            target="_blank"
          >
            Politique de confidentialité
          </a>
        </div>
        <div class="mb-4 mr-4 md:mb-0">
          <a
            :href="djustPayTermsLink"
            target="_blank"
          >
            Conditions générales d'utilisation Djust Pay
          </a>
        </div>
      </div>
      <div v-if="!channel?.whiteLabel" class="flex space-x-5 md:space-x-10">
        <a
          class="flex h-10 w-10 items-center justify-center rounded-full bg-white"
          href="https://www.youtube.com/channel/UCP-ZzEGFZ4rtW0Yx8u1ZDMQ"
          target="_blank"
          @click="
            sendGtmEvent('social_media_click', {
              link_url: $event.currentTarget.href,
              origin_url: router.currentRoute.value.fullPath,
            })
          "
        >
          <YoutubeIconComponent class="fill-primary stroke-white" />
        </a>
        <a
          class="flex h-10 w-10 items-center justify-center rounded-full bg-white"
          href="https://twitter.com/QANTIS_co"
          target="_blank"
          @click="
            sendGtmEvent('social_media_click', {
              link_url: $event.currentTarget.href,
              origin_url: router.currentRoute.value.fullPath,
            })
          "
        >
          <TwitterIconComponent class="fill-primary stroke-white" />
        </a>
        <a
          class="flex h-10 w-10 items-center justify-center rounded-full bg-white"
          href="https://www.linkedin.com/company/qantis-co/mycompany/"
          target="_blank"
          @click="
            sendGtmEvent('social_media_click', {
              link_url: $event.currentTarget.href,
              origin_url: router.currentRoute.value.fullPath,
            })
          "
        >
          <LinkedinIconComponent class="fill-primary stroke-primary" />
        </a>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'

import router, { PageList } from '@/vuejs/router'
import { AccountPageList } from '@/vuejs/router/pages-list'
import { useChannelStore } from '@/vuejs/stores/channel'
import { betterTextColor, getImage } from '@/vuejs/services/utils'
// TODO (MKP-1411): Import à décommenter quand les fonctionnalités Favoris/Paniers seront disponibles via DJUST
// import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'
import { sendGtmEvent } from '@/vuejs/services/gtm'

import QantisLogoComponent from '@/vuejs/modules/shared/icon/QantisLogoComponent.vue'
import coqVertLogo from '@/vuejs/assets/img/coq_vert_footer_logo.png'
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'
import YoutubeIconComponent from '@/vuejs/modules/shared/icon/YoutubeIconComponent.vue'
import TwitterIconComponent from '@/vuejs/modules/shared/icon/TwitterIconComponent.vue'
import LinkedinIconComponent from '@/vuejs/modules/shared/icon/LinkedinIconComponent.vue'

const coqVertLogoImg = getImage(coqVertLogo)
const djustPayTermsLink = 'https://www.adyen.com/legal/adyen-terms-and-conditions'

const channelStore = useChannelStore()

const {
  channel,
  formattedPhoneNumber,
  channelDocuments,
  channelGeneralTermsOfUseLink,
  channelLegalTermsLink,
  channelPrivacyPolicyLink,
} = storeToRefs(channelStore)

const memberArea = computed((): string => {
  return channel?.value?.options?.FOOTER_TITLE_ACCOUNT ?? 'Mon espace adhérent'
})
</script>

<style>
@reference '@/style/main.css';

.three-column-list {
  @apply m-0 columns-1 p-0 md:columns-2 lg:columns-3;
}

.footer {
  @apply w-full bg-primary;
}

.footer h3 {
  @apply text-left text-[24px] font-bold leading-6 md:text-[20px] md:leading-7 lg:leading-8;
}

.footer .second-part p,
.footer li {
  @apply text-left text-base font-normal leading-6 md:text-lg;
}

.bloc-contact-footer {
  @apply h-auto bg-secondary;
}

.bloc-contact-footer > div > div {
  @apply m-5 sm:mx-12 xl:mx-24;
}


.footer ul li a:hover {
  @apply border-b-2 border-secondary;
}

.footer .social-network-logo div {
  @apply float-left mr-2.5 box-border h-[40px] w-[40px] rounded-full border border-white p-2.5;
}
</style>
