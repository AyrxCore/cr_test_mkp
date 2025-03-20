<template>
  <template v-if="!userAccounts.length">
    <div class="flex flex-col justify-between md:space-x-8">
      <div class="flex w-full flex-col">
        <form
          class="flex w-full flex-col items-center"
          @submit.prevent="loginSubmit"
        >
          <h1 class="text-title-primary mb-4 xl:text-2xl">Connexion</h1>
          <AlertSharedComponent v-if="showAlert" />
          <div class="lg:w-5/6">
            <div class="relative mb-3 mt-3">
              <label for="login-email">
                Votre adresse email professionnelle
              </label>
              <input
                id="login-email"
                v-model.trim="username"
                :disabled="isLoading"
                autocomplete="username"
                class="input !border-solid !border-black !py-1 !pr-16 !ring-black md:!py-2 xl:!py-4"
                placeholder="Saisir votre adresse email professionnelle"
                required
                type="email"
              />
              <span
                class="absolute inset-y-0 right-0 flex items-center pr-8 text-gray-700"
              >
                <MailIcon />
              </span>
            </div>
            <div class="relative mb-3 mt-3">
              <label for="login-password">Votre mot de passe</label>
              <input
                v-model="password"
                :disabled="isLoading"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                class="input !border-solid !border-black !py-1 !pr-16 !ring-black md:!py-2 xl:!py-4"
                placeholder="**********"
                required
              />
              <span
                class="absolute inset-y-0 right-0 flex cursor-pointer items-center pr-6 pt-6 text-gray-500"
                @click="toggleShowPassword"
              >
                <EyeSlashIcon v-if="showPassword" />
                <EyeIcon v-else />
              </span>
            </div>
            <a
              class="text-sm font-medium text-gray-500 underline"
              href="javascript:void(0)"
              @click="
                eventClick('click_prehome_forgot_pass', '/login/reset-password')
              "
            >
              Mot de passe oublié ?
            </a>
            <div class="mt-5 flex w-full justify-center">
              <ButtonComponent
                :is-loading="isLoading"
                class="button-primary w-full font-bold md:w-1/2"
                type="submit"
                @click="sendGaEvent('click_prehome_login')"
              >
                Se connecter
              </ButtonComponent>
            </div>
            <div
              class="mb-2 mt-5 flex w-full flex-col items-center justify-center md:justify-start xl:mb-3"
            >
              <p
                class="mx-auto mb-2 flex text-center text-xs text-gray-700 md:w-auto md:text-sm xl:mb-3 xl:text-left xl:text-base"
              >
                {{ isFirst }}
                <br />
                Cliquez ci-dessous 👇
              </p>
              <a
                class="button button-primary-outline"
                href="/login/first-signin"
                @click="
                  eventClick('click_prehome_activate', '/login/first-signin')
                "
              >
                Activer mon compte
                <ArrowRightIcon
                  class="ml-2 h-[14px] w-[14px] !fill-primary !stroke-primary"
                />
              </a>
            </div>
            <div class="mb-8 mt-6 flex md:mt-10 xl:mt-14">
              <HelpIconComponent class="mr-2 w-16 fill-primary md:flex" />
              <p class="flex flex-col text-gray-500">
                <span class="font-bold text-gray-700">Besoin d'aide ?</span>
                <span class="text-xs md:text-sm">
                  Contactez-nous au :
                  <a
                    :href="`tel:${channel?.phoneNumber}`"
                    class="text-secondary underline lg:text-right"
                    @click="
                      eventClick(
                        'click_prehome_tel',
                        `tel:${channel?.phoneNumber}`,
                      )
                    "
                    >{{ channelPhoneNumber }}</a
                  >
                  du lundi au vendredi de 8h30 à 18h ou via
                  <button
                    class="text-secondary underline"
                    type="button"
                    @click="contactModal('click_prehome_email')"
                  >
                    {{ channel?.email }}
                  </button></span
                >
              </p>
            </div>
          </div>
        </form>
      </div>
      <p
        v-if="!channel?.whiteLabel"
        class="hidden pb-2 text-[0.6rem] text-gray-500 lg:block"
      >
        La marketplace QANTIS est un espace fermé et réservé aux adhérents de
        notre centrale d'achats. Envie de rejoindre nos plus de 30 000 adhérents
        ? <a href="https://qantis.co/centrale-dachats/">C'est par ici !</a>
      </p>
      <div
        class="flex justify-between pb-2 text-center text-[0.5rem] md:mt-0 md:hidden"
      >
        <a
          v-if="channelDocuments?.legalTerms"
          :href="channelLegalTermsLink"
          class="text-gray-500"
          target="_blank"
          @click="eventClick('click_prehome_mentions', channelLegalTermsLink)"
        >
          Mentions légales
        </a>
        <a
          v-if="channelDocuments?.privacyPolicy"
          :href="channelPrivacyPolicyLink"
          class="text-gray-500"
          target="_blank"
          @click="eventClick('click_prehome_polconf', channelPrivacyPolicyLink)"
        >
          Politique de confidentialité
        </a>
        <a
          v-if="channelDocuments?.generalTermsOfUse"
          :href="channelGeneralTermsOfUseLink"
          class="text-gray-500"
          target="_blank"
          @click="eventClick('click_prehome_cgu', channelGeneralTermsOfUseLink)"
        >
          Conditions Générales d'Utilisation
        </a>
      </div>
    </div>
  </template>
  <template v-else-if="userAccounts.length > 1">
    <div class="flex flex-col justify-between md:flex-row md:space-x-8">
      <div class="flex w-full">
        <div class="flex w-full flex-col">
          <div
            v-if="showAlert && !showCGUModal"
            class="flex w-full justify-center"
          >
            <AlertSharedComponent />
          </div>
          <div
            class="mb-6 flex w-full flex-col items-center justify-center md:mb-10"
          >
            <h1 class="text-title-primary mb-6">Bonjour,</h1>
            <div class="text-base">
              Nous avons plusieurs SIRET enregistrés à votre nom. Choisissez une
              des sociétés pour accéder à votre service achats:
            </div>
          </div>
          <div
            v-for="(account, id) in userAccounts"
            :key="id"
            class="mt-2 rounded-lg border border-primary"
          >
            <label
              v-if="account.externalApiData"
              :for="`account-radio-${id}`"
              class="flex cursor-pointer items-center rounded-md bg-white px-4 py-2"
            >
              <input
                :id="`account-radio-${id}`"
                v-model="accountSelectedId"
                :value="account.id"
                class="mr-4 border-primary"
                type="radio"
                @change="onChangeBuyer(account.acceptCGU)"
              />
              <div>
                <div class="font-bold uppercase text-primary">
                  {{ account.externalApiData.name }}
                </div>
                <div
                  v-if="account.externalApiData.number"
                  class="font-bold text-gray-700"
                >
                  SIRET : {{ account.externalApiData.number }}
                </div>
              </div>
            </label>
          </div>
          <div class="mt-10 flex justify-center md:justify-end">
            <ButtonComponent
              :is-loading="isLoading"
              class="button-primary w-auto"
              type="button"
              @click="onAccountClick"
            >
              Valider
            </ButtonComponent>
          </div>
          <div class="mt-8 w-full justify-center md:mt-10 xl:mt-14">
            <div
              class="flex flex-col justify-center px-2 text-center text-gray-500"
            >
              <p class="px-12 text-sm">
                {{ contactTo }} est à votre écoute<br />
                du lundi au vendredi de 8h30 à 18h au
                <a
                  :href="`tel:${channel?.phoneNumber}`"
                  class="text-secondary underline lg:text-right"
                  >{{ channelPhoneNumber }}</a
                >
                ou par mail
              </p>
              <ButtonComponent
                class="button-primary-outline mx-auto mt-5 w-full md:w-auto lg:mt-10"
                type="button"
                @click="showContactForm = true"
              >
                <MailIcon
                  class="h-[15px] w-[15px] fill-primary stroke-white text-secondary hover:!fill-secondary"
                />
                <span> {{ contactAdherentsService }}</span>
              </ButtonComponent>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  <CGUModal v-if="showCGUModal" class="modal" @valid-cgu="valideCGU" />
  <ContactModal
    v-if="showContactForm"
    :is-loading="isLoading"
    class="modal"
    @cancel="showContactForm = false"
  />
  <StellantisModal
    v-if="showStellantisModal"
    class="modal"
    @accept-stellantis="valideStellantis"
  />
</template>
<script lang="ts" setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'

import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import ArrowRightIcon from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CGUModal from '@/vuejs/modules/login/component/CGUModal.vue'
import StellantisModal from '@/vuejs/modules/login/component/StellantisModal.vue'
import ContactModal from '@/vuejs/modules/contact/component/ContactModal.vue'
import EyeIcon from '@/vuejs/modules/shared/icon/EyeIconComponent.vue'
import EyeSlashIcon from '@/vuejs/modules/shared/icon/EyeSlashIconComponent.vue'
import HelpIconComponent from '@/vuejs/modules/shared/icon/HelpIconComponent.vue'
import MailIcon from '@/vuejs/modules/shared/icon/MailIconComponent.vue'

import { getUrlParam } from '@/vuejs/services/utils'
import { AlertType } from '@/vuejs/types/Alert'
import { useAlertStore } from '@/vuejs/stores/alert'
import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'
import { Account } from '@/vuejs/types/Account'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const username = ref<string>('')
const password = ref<string>('')
const userAccounts = ref<Account[]>([])
const isLoading = ref<boolean>(false)
const userStore = useUserStore()
const alertStore = useAlertStore()
const showPassword = ref<boolean>(false)
const accountSelectedId = ref<string>(null)
const accountAcceptCGU = ref(null)
const showCGUModal = ref<boolean>(false)
const showContactForm = ref<boolean>(false)
const showStellantisModal = ref<boolean>(false)
const selectedAccount = ref(null)

const { show: showAlert } = storeToRefs(alertStore)

const {
  channel,
  channelDocuments,
  formattedPhoneNumber: channelPhoneNumber,
  channelGeneralTermsOfUseLink,
  channelLegalTermsLink,
  channelPrivacyPolicyLink,
} = storeToRefs(useChannelStore())

const handleAccountSelection = async (accountId: string, account?: Account) => {
  if (!accountId) {
    alertStore.setShow(
      'Vous devez sélectionner un compte acheteur pour vous connecter',
      AlertType.danger,
    )
    return
  }

  const selected =
    account || userAccounts.value.find((acc) => acc.id === accountId)
  if (!selected) {
    alertStore.setShow('Compte non trouvé', AlertType.danger)
    return
  }

  selectedAccount.value = selected

  if (!selected.acceptCGU) {
    showCGUModal.value = true
    return
  }

  if (
    selected.adherent?.showModalStellantis &&
    !selected.adherent?.stellantisModalValidated
  ) {
    showStellantisModal.value = true
    return
  }

  await proceedWithAccountSelection(accountId)
}

const loginSubmit = async () => {
  if (isLoading.value) return
  alertStore.setClose()
  isLoading.value = true

  try {
    const accounts = await userStore.authenticate({
      username: username.value.toLowerCase(),
      password: password.value,
    })

    if (!accounts.length) {
      isLoading.value = false
      return false
    }

    if (accounts.length > 1) {
      userAccounts.value = accounts
      isLoading.value = false
    } else {
      const [firstAccount] = accounts
      accountSelectedId.value = firstAccount.id
      await handleAccountSelection(firstAccount.id, firstAccount)
    }
  } catch (error) {
    console.error("Erreur lors de l'authentification:", error)
    alertStore.setShow('Erreur de connexion', AlertType.danger)
    isLoading.value = false
  }
}

const proceedWithAccountSelection = async (accountId: string) => {
  isLoading.value = true

  try {
    await userStore.selectUserAccount(accountId)
    window.dataLayer?.push({ event: 'login' })
    const target = getUrlParam('target')
    document.location.href = target ? `/${target}` : '/'
  } catch (error) {
    console.error('Erreur lors de la sélection du compte:', error)
    alertStore.setShow('Erreur de sélection du compte', AlertType.danger)
  } finally {
    isLoading.value = false
  }
}

const onAccountClick = async () => {
  await handleAccountSelection(accountSelectedId.value)
  sendGaEvent('click_siret_valider')
}

const onChangeBuyer = (acceptCgu: boolean) => {
  accountAcceptCGU.value = acceptCgu
}

const valideCGU = async () => {
  showCGUModal.value = false

  if (
    selectedAccount.value?.adherent?.showModalStellantis &&
    !selectedAccount.value?.adherent?.stellantisModalValidated
  ) {
    showStellantisModal.value = true
  } else {
    await proceedWithAccountSelection(accountSelectedId.value)
  }
}

const valideStellantis = async () => {
  showStellantisModal.value = false
  await proceedWithAccountSelection(accountSelectedId.value)
}

const eventClick = (eventName: string, url: string) => {
  try {
    sendGaEvent(eventName)
  } catch (e) {
    console.error(e)
  } finally {
    document.location.href = url
  }
}

const contactModal = (eventName: string) => {
  sendGaEvent(eventName)
  showContactForm.value = true
}

const isFirst = computed((): string => {
  return (
    channel?.value?.options?.PRE_HOME_TEXT_LEFT_COLUMN ??
    "Vous êtes adhérent et c'est votre première fois ici ?"
  )
})

const contactTo = computed((): string => {
  return (
    channel?.value?.options
      ?.PRE_HOME_TEXT_FIRST_CONNECTION_CHANGE_OF_PASSWORD ??
    'Notre service adhérents'
  )
})

const contactAdherentsService = computed((): string => {
  return (
    channel?.value?.options
      ?.PRE_HOME_BUTTON_FIRST_CONNECTION_CHANGE_OF_PASSWORD ??
    'Joindre le service adhérents'
  )
})

const toggleShowPassword = () => {
  showPassword.value = !showPassword.value
}
</script>
