<template>
  <div
    class="footer pb-4"
    :style="{
      color: betterTextColor('primary'),
    }"
  >
    <div
      class="second-part grid grid-cols-1 gap-4 px-8 pt-10 pb-5 md:grid-cols-2 md:gap-8 md:px-16 xl:grid-cols-4 xl:gap-16"
    >
      <div class="mb-2 md:mb-4">
        <h3 class="mb-2 md:mb-6 lg:mb-7">
          {{ channel.name }}
        </h3>
        <p>
          {{ channel.options['TEXT_FOOTER'] }}
        </p>
      </div>
      <div class="mb-2 md:mb-4">
        <h3 class="mb-2 md:mb-6 lg:mb-7">À propos</h3>
        <ul>
          <li>
            <RouterLink
              :to="{ name: PageList.CONTACT_PAGE }"
              @click="sendGaEvent('click_footer_contact')"
            >
              Nous contacter
            </RouterLink>
          </li>
          <li>
            <a
              target="_blank"
              :href="channelGeneralTermsOfUseLink"
              @click="sendGaEvent('click_footer_cgu')"
            >
              Conditions générales d'utilisation
            </a>
          </li>
          <li>
            <a
              target="_blank"
              :href="channelLegalTermsLink"
              @click="sendGaEvent('click_footer_mentions_legales')"
            >
              Mentions légales
            </a>
          </li>
          <li>
            <a
              target="_blank"
              :href="channelPrivacyPolicyLink"
              @click="sendGaEvent('click_footer_politique_confidentialite')"
            >
              Politique de confidentialité
            </a>
          </li>
        </ul>
      </div>
      <div class="mb-2 md:mb-4">
        <h3 class="mb-2 md:mb-6 lg:mb-7">Votre espace adhérents</h3>
        <ul>
          <li>
            <RouterLink
              :to="{ name: AccountPageList.ACCOUNT }"
              @click="sendGaEvent('click_footer_account')"
            >
              Mon compte
            </RouterLink>
          </li>
          <li>
            <RouterLink
              :to="{ name: AccountPageList.ORDERS }"
              @click="sendGaEvent('click_footer_mes_commandes')"
            >
              Mes commandes
            </RouterLink>
          </li>
        </ul>
      </div>
      <div class="mb-2 md:mb-4">
        <div class="flex flex-col md:flex-row md:justify-between lg:flex-col">
          <div class="md:mt-0">
            <h3 class="mb-6 lg:mb-7">Paiement sécurisé par&nbsp;:</h3>
            <img
              :src="lemonwayLogoImg"
              alt="header"
              class="logo-lemway mt-[0.5rem]"
            />
          </div>
        </div>
      </div>
    </div>
    <div v-if="!channel?.whiteLabel">
      <div
        class="mt-4 flex flex-col-reverse px-8 pb-5 md:grid md:grid-cols-2 md:gap-8 md:px-16 md:pb-20 xl:mt-0 xl:grid-cols-4 xl:gap-16"
      >
        <div class="flex">
          <QantisLogoComponent class="mt-[0.5rem] fill-primary stroke-white" />
        </div>
        <div class="hidden xl:flex">
          <img
            :src="coqVertLogoImg"
            alt="header"
            class="mt-[0.5rem] h-20 object-contain"
          />
        </div>
        <div class="mb-6 md:mb-0">
          <h3 class="mb-6 md:mb-4 lg:mb-7">Retrouvez l'actualité de QANTIS</h3>
          <div class="flex space-x-5 md:space-x-10">
            <a
              href="https://www.youtube.com/channel/UCP-ZzEGFZ4rtW0Yx8u1ZDMQ"
              class="flex h-10 w-10 items-center justify-center rounded-full bg-white"
              target="_blank"
            >
              <YoutubeIconComponent class="fill-primary stroke-white" />
            </a>
            <a
              href="https://twitter.com/QANTIS_co"
              class="flex h-10 w-10 items-center justify-center rounded-full bg-white"
              target="_blank"
            >
              <TwitterIconComponent class="fill-primary stroke-white" />
            </a>
            <a
              href="https://www.linkedin.com/company/qantis-co/mycompany/"
              class="flex h-10 w-10 items-center justify-center rounded-full bg-white"
              target="_blank"
            >
              <LinkedinIconComponent class="fill-primary stroke-primary" />
            </a>
          </div>
        </div>
      </div>
      <div class="hidden py-6 md:mx-16 md:flex md:justify-center md:border-t">
        <p class="mt-2">
          QANTIS, 185, allée des Cyprès, 69760 LIMONEST, FRANCE
        </p>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import lemonwayLogo from '@/vuejs/assets/img/lemonway_footer_logo.png'
import coqVertLogo from '@/vuejs/assets/img/coq_vert_footer_logo.png'
import { betterTextColor, getImage } from '@/vuejs/services/utils'
import { useChannelStore } from '@/vuejs/stores/channel'
import { AccountPageList } from '@/vuejs/router/pages-list'
import { PageList } from '@/vuejs/router'

import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

import YoutubeIconComponent from '@/vuejs/modules/shared/icon/YoutubeIconComponent.vue'
import TwitterIconComponent from '@/vuejs/modules/shared/icon/TwitterIconComponent.vue'
import LinkedinIconComponent from '@/vuejs/modules/shared/icon/LinkedinIconComponent.vue'
import QantisLogoComponent from '@/vuejs/modules/shared/icon/QantisLogoComponent.vue'
import { storeToRefs } from 'pinia'

const lemonwayLogoImg = getImage(lemonwayLogo)
const coqVertLogoImg = getImage(coqVertLogo)

const {
  channel,
  channelGeneralTermsOfUseLink,
  channelLegalTermsLink,
  channelPrivacyPolicyLink,
} = storeToRefs(useChannelStore())
</script>

<style lang="scss">
.three-column-list {
  @apply m-0 columns-1 p-0 md:columns-2 lg:columns-3;
}

.footer {
  @apply w-full bg-primary;
}

.footer h3 {
  @apply text-left text-[18px] font-bold leading-6 md:text-[20px] md:leading-7 lg:text-[24px] lg:leading-8;
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

.footer .logo-lemway {
  height: 48px;
  width: 209px;
  top: 289px;
}

.footer ul li a:hover {
  @apply border-b-2 border-secondary;
}

.footer .social-network-logo div {
  @apply float-left mr-2.5 box-border h-[40px] w-[40px] rounded-full border border-white p-2.5;
}
</style>
