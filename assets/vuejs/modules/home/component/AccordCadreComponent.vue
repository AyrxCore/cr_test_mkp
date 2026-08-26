<template>
  <div
    :class="[
      'relative mb-4 flex w-full flex-col items-center rounded-lg border-2 border-solid p-4',
      isInShowcase ? 'border-gray-400' : 'border-secondary',
    ]"
  >
    <div
      :class="{ 'justify-between': accord.newTarifNotification }"
      class="flex h-[50px] w-full items-start justify-end"
    >
      <div
        v-if="accord.newTarifNotification"
        class="flex items-center rounded-sm border border-secondary p-1 text-sm text-secondary"
      >
        <BellRingOutlineIconComponent class="fill-secondary" />
        <span class="ml-1">Nouveau tarif</span>
      </div>
      <LockIconComponent
        v-if="isInShowcase"
        :stroke="channelSecondaryColor"
        class="mr-2"
      />
      <!-- <AddFavoriteComponent
        v-else
        :favorites-product="accord.favorites"
        :product-id="accord.id"
        :product-name="accordName"
        class="ml-4"
      /> -->
      <span
        v-else-if="accordBadgeLabel"
        class="rounded-md bg-secondary px-2 py-1 text-sm text-white"
      >
        {{ accordBadgeLabel }}
      </span>
    </div>
    <Component
      :is="!isInShowcase ? 'RouterLink' : 'div'"
      :class="
        !isInShowcase
          ? 'block'
          : [
              { 'pointer-events-none': contactRequested },
              'text-wrap!',
              'cursor-pointer',
            ]
      "
      :to="
        !isInShowcase
          ? {
              name: ProductPageList.ACCORD_CADRE,
              params: { slug: accord.slug },
            }
          : null
      "
      @click="
        !isInShowcase
          ? sendGtmEvent('fat_click', {
              link_url: router.resolve({
                name: ProductPageList.ACCORD_CADRE,
                params: { slug: accord.slug },
              }).fullPath,
              origin_url: router.currentRoute.value.fullPath,
            })
          : $emit('show-showcase-modal', accord)
      "
    >
      <div class="flex h-[250px] flex-col items-center">
        <div class="my-1 flex w-full items-center">
          <div
            class="mx-auto flex h-[110px] max-w-[200px] items-center justify-center rounded-lg"
          >
            <component
              :is="isInShowcase ? 'div' : 'RouterLink'"
              :to="isInShowcase ? null : productLink"
              class="block"
            >
              <img
                :alt="`Image ${accordName}`"
                :class="{ 'pointer-events-none': contactRequested }"
                :src="partnerLogo"
                class="max-h-[90px] max-w-full w-auto object-contain cursor-pointer"
                @click="handleShowcaseModal"
              />
            </component>
          </div>
        </div>

        <div class="flex flex-1 w-full flex-col">
          <h3
            :class="{ 'pointer-events-none': contactRequested }"
            class="truncate-custom truncate-custom-2 text-title-default-size my-2 cursor-pointer text-center font-bold text-primary md:text-xl lg:text-lg"
          >
            <RouterLink
              v-if="!isInShowcase"
              :to="{
                name: ProductPageList.ACCORD_CADRE,
                params: { slug: accord.slug },
              }"
              class="block"
            >
              {{ accordName }}
            </RouterLink>
            <span v-else @click="$emit('show-showcase-modal', accord)">{{
              accordName
            }}</span>
          </h3>

          <div class="mt-auto flex w-full justify-center">
            <ButtonComponent
              v-if="isInShowcase"
              :class="[
                contactButtonClass,
                { 'pointer-events-none': contactRequested },
                'text-wrap!',
              ]"
            >
              {{ buttonText }}
              <CheckIconComponent
                v-if="contactRequested"
                :stroke="channelPrimaryColor"
                class="ml-2"
              />
            </ButtonComponent>
            <div
              v-else
              :class="[
                'button flex items-center justify-center',
                accordButtonClass,
              ]"
            >
              {{ accordButtonText }}
            </div>
          </div>
        </div>
      </div>
    </Component>
  </div>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'
import { storeToRefs } from 'pinia'
import { RouteLocationRaw } from 'vue-router'

import router from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { Product } from '@/vuejs/types/Product'
import { AdherentTarifShowcase } from '@/vuejs/types/AdherentTarifShowcase'
import { AccountAccordCadreStatus } from '@/vuejs/types/AccountAccordCadre'
import { ACCORD_CADRE_TYPE } from '@/vuejs/services/const'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import LockIconComponent from '@/vuejs/modules/shared/icon/LockIconComponent.vue'
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'
import BellRingOutlineIconComponent from '@/vuejs/modules/shared/icon/BellRingOutlineIconComponent.vue'

const { channelPrimaryColor, channelSecondaryColor } =
  storeToRefs(useChannelStore())
const { adherentTarifShowcases } = storeToRefs(useUserStore())

const props = defineProps({
  accord: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const emit = defineEmits(['click-accord-cadre-card', 'show-showcase-modal'])

const partnerLogo = computed<string>(
  () => props.accord.accordCadreContent?.listBlocks?.bannerBlock?.logoUrl,
)

const accordName = computed<string>(() => props.accord.accordCadreContent?.name)

const isInShowcase = computed<boolean>(() =>
  adherentTarifShowcases.value.some(
    (showcase) => showcase.accordId === props.accord.accordId,
  ),
)

const showcase = computed<AdherentTarifShowcase | undefined>(() =>
  adherentTarifShowcases.value.find(
    (showcase) => showcase.accordId === props.accord.accordId,
  ),
)

const contactRequested = computed<boolean>(() =>
  showcase.value ? showcase.value.contactRequested : false,
)

const buttonText = computed<string>(() =>
  isInShowcase.value
    ? contactRequested.value
      ? 'Demande de rappel effectuée'
      : 'Être rappelé pour en bénéficier'
    : "Consulter l'accord-cadre",
)

const productLink = computed<RouteLocationRaw>(() => ({
  name: ProductPageList.ACCORD_CADRE,
  params: { slug: props.accord.slug },
}))

const contactButtonClass = computed<string>(() =>
  contactRequested.value ? 'button-primary-outline' : 'button-primary',
)

const accordBadgeLabel = computed<string | undefined>(
  () =>
    props.accord.accordCadreContent?.listBlocks?.bannerBlock?.badgeTextBottom,
)

const effectiveStatus = computed<AccountAccordCadreStatus>(() => {
  if (props.accord.accordCadreContent?.type === ACCORD_CADRE_TYPE.DIRECT) {
    return AccountAccordCadreStatus.ACTIVATED
  }
  return props.accord.accountAccordCadre?.status ?? AccountAccordCadreStatus.NOT_ACTIVATED
})

const accordButtonText = computed<string>(() => {
  switch (effectiveStatus.value) {
    case AccountAccordCadreStatus.PENDING:
    case AccountAccordCadreStatus.ACTIVATED:
      return 'Consulter'
    case AccountAccordCadreStatus.NOT_ACTIVATED:
    default:
      return 'A activer'
  }
})

const accordButtonClass = computed<string>(() => {
  switch (effectiveStatus.value) {
    case AccountAccordCadreStatus.PENDING:
    case AccountAccordCadreStatus.ACTIVATED:
      return 'button-primary-outline'
    case AccountAccordCadreStatus.NOT_ACTIVATED:
    default:
      return 'button-primary'
  }
})

const handleShowcaseModal = () => {
  if (isInShowcase.value) {
    emit('show-showcase-modal', props.accord)
  }
}
</script>
