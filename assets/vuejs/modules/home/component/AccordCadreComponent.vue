<template>
  <div
    :class="[
      'flex flex-col items-center rounded-lg border-4 border-solid p-4',
      isInShowcase ? 'border-gray-400' : 'border-secondary',
    ]"
  >
    <div class="flex h-[50px] w-full items-center justify-end">
      <LockIconComponent
        v-if="isInShowcase"
        :stroke="channelSecondaryColor"
        class="mr-2"
      />
      <div
        :class="{
          'bg-secondary': !isInShowcase,
          'bg-gray-400': isInShowcase,
        }"
        :style="{
          color: betterTextColor('secondary'),
        }"
        class="rounded-sm p-1 text-sm"
      >
        Accord-cadre
      </div>
    </div>

    <div class="flex h-full w-full flex-col items-center">
      <div class="my-1 flex w-full items-center">
        <div
          class="flex h-[200px] max-w-[200px] items-center justify-center rounded-lg sm:mx-auto"
          @click="
            $emit('click-img', {
              partenaire_name: accord.seller.name,
              partenaire_id: accord.seller.id,
            })
          "
        >
          <component
            :is="isInShowcase ? 'div' : 'RouterLink'"
            :to="isInShowcase ? null : productLink"
            class="block"
          >
            <img
              :alt="`Image ${accord.name}`"
              :class="{ 'pointer-events-none': contactRequested }"
              :src="properties.logo_partenaire"
              class="max-h-[150px] cursor-pointer items-center sm:flex md:max-h-[139px] lg:max-h-[191px] lg:w-full lg:max-w-max"
              @click="handleShowcaseModal"
            />
          </component>
        </div>
      </div>

      <div class="flex h-3/5 w-full flex-col justify-between">
        <div class="h-[30%]">
          <h3
            :class="{ 'pointer-events-none': contactRequested }"
            class="truncate-custom truncate-custom-2 text-title-default-size my-2 cursor-pointer text-center font-bold text-primary md:text-xl lg:text-lg"
            @click="
              $emit('click-title', {
                partenaire_name: accord.seller.name,
                partenaire_id: accord.seller.id,
              })
            "
          >
            <RouterLink
              v-if="!isInShowcase"
              :to="{
                name: ProductPageList.ACCORD_CADRE,
                params: { slug: accord.slug },
              }"
              class="block"
            >
              {{ accord.name }}
            </RouterLink>
            <span v-else @click="$emit('show-showcase-modal', accord)">{{
              accord.name
            }}</span>
          </h3>
        </div>

        <div>
          <p
            class="description truncate-custom truncate-custom-3 mb-4 px-2 text-center"
            v-html="accord.description"
          />
        </div>

        <div class="mt-1 flex w-full justify-center">
          <ButtonComponent
            v-if="isInShowcase"
            :class="[
              contactButtonClass,
              { 'pointer-events-none': contactRequested },
              '!text-wrap',
            ]"
            @click="$emit('show-showcase-modal', accord)"
          >
            {{ buttonText }}
            <CheckIconComponent
              v-if="contactRequested"
              :stroke="channelPrimaryColor"
              class="ml-2"
            />
          </ButtonComponent>
          <RouterLink
            v-else
            :style="{ color: betterTextColor('primary') }"
            :to="{
              name: ProductPageList.ACCORD_CADRE,
              params: { slug: accord.slug },
            }"
            class="button button-primary flex items-center justify-center"
            @click="
              $emit('click-cta', {
                partenaire_name: accord.seller.name,
                partenaire_id: accord.seller.id,
              })
            "
          >
            Consulter l'accord&#8209;cadre
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'
import { RouteLocationRaw } from 'vue-router'
import { storeToRefs } from 'pinia'

import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'

import { ProductPageList } from '@/vuejs/router/pages-list'

import { Product } from '@/vuejs/types/Product'
import { AdherentTarifShowcase } from '@/vuejs/types/AdherentTarifShowcase'

import { betterTextColor } from '@/vuejs/services/utils'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import LockIconComponent from '@/vuejs/modules/shared/icon/LockIconComponent.vue'
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'

const { channelPrimaryColor, channelSecondaryColor } =
  storeToRefs(useChannelStore())
const { adherentTarifShowcases } = storeToRefs(useUserStore())

const props = defineProps({
  accord: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const emit = defineEmits([
  'click-cta',
  'click-title',
  'click-img',
  'show-showcase-modal',
])

const properties = computed<any[]>(() => props.accord.properties)

const isInShowcase = computed<boolean>(() =>
  adherentTarifShowcases.value.some(
    (showcase) => showcase.accordId === properties.value['accord-id'],
  ),
)

const showcase = computed<AdherentTarifShowcase | undefined>(() =>
  adherentTarifShowcases.value.find(
    (showcase) => showcase.accordId === properties.value['accord-id'],
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

const handleShowcaseModal = () => {
  if (isInShowcase.value) {
    emit('show-showcase-modal', props.accord)
  }
}
</script>
