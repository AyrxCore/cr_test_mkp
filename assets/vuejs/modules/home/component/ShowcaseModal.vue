<template>
  <div class="modal-overlay" @click="$emit('cancel')">
    <div
      :class="!accordShowcaseIsRequested ? 'bg-primary' : 'bg-secondary'"
      class="z-9999 fixed mx-3 w-full rounded p-3 md:w-[60%] md:p-5 lg:top-20 lg:mx-0"
      @click.stop=""
    >
      <div class="w-full">
        <div class="flex justify-end">
          <ButtonComponent
            class="!h-5 !px-3 text-xl font-bold !text-white"
            type="button"
            @click="onCancelClick"
          >
            <CloseIconComponent />
          </ButtonComponent>
        </div>
        <h3
          class="text-title-primary mb-3 flex flex-col px-5 text-center !text-white"
        >
          {{
            !accordShowcaseIsRequested
              ? 'Vous souhaitez bénéficier de ce partenaire ?'
              : 'Merci !'
          }}
        </h3>
        <div
          class="flex flex-col items-center px-3 text-center text-xl text-white md:px-8"
        >
          <div class="my-4">
            <div v-if="!accordShowcaseIsRequested" class="mb-4 text-center">
              <div>
                Cliquez sur le bouton ci-dessous pour confirmer votre requête et
                notre équipe reprendra contact avec vous.
              </div>
            </div>
            <span v-else>
              Nous avons bien pris connaissance de votre demande pour bénéficier
              des conditions du partenaire
            </span>
          </div>

          <div
            v-if="!accordShowcaseIsRequested"
            class="my-4 flex w-full justify-around"
          >
            <ButtonComponent
              class="button-primary-outline-white"
              type="button"
              @click="onCancelClick"
            >
              Annuler
            </ButtonComponent>
            <ButtonComponent
              :is-loading="showcaseLoading"
              class="button-secondary mr-2"
              @click="handleContactRequest"
            >
              Je souhaite être rappelé
            </ButtonComponent>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, PropType, ref } from 'vue'
import { storeToRefs } from 'pinia'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CloseIconComponent from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'
import { useUserStore } from '@/vuejs/stores/user'
import { useAdherentTarifShowcaseStore } from '@/vuejs/stores/adherentTarifShowcase'
import { AdherentTarifShowcase } from '@/vuejs/types/AdherentTarifShowcase'
import { Product } from '@/vuejs/types/Product'
import { notifyError } from '@/vuejs/services/utils'

const props = defineProps({
  accord: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const adherentTarifShowcaseStore = useAdherentTarifShowcaseStore()
const { adherentTarifShowcases } = storeToRefs(useUserStore())
const emit = defineEmits(['cancel', 'submitFavorite', 'changeValue'])
const showcaseLoading = ref<boolean>(false)

const properties = computed<any[]>(() => props.accord.properties)

const showcase = computed<AdherentTarifShowcase | undefined>(() =>
  adherentTarifShowcases.value.find(
    (showcase) => showcase.accordId === properties.value['accord-id'],
  ),
)

const contactRequested = computed<boolean>(() =>
  showcase.value ? showcase.value.contactRequested : false,
)

const showcaseId = computed<string | null>(() =>
  showcase.value ? showcase.value.id : null,
)

const accordShowcaseIsRequested = computed<boolean>(() =>
  adherentTarifShowcases.value.some((showcase) => {
    return (
      showcase.accordId === props.accord.properties['accord-id'] &&
      showcase.contactRequested
    )
  }),
)

const onCancelClick = () => {
  emit('cancel')
}

async function handleContactRequest() {
  showcaseLoading.value = true
  if (contactRequested.value || !showcaseId.value) {
    return
  }

  try {
    await adherentTarifShowcaseStore.handleRequestContactForShowcase(
      showcaseId.value,
      props.accord.name,
      properties.value['accord-id'],
    )
  } catch (error) {
    onCancelClick()
    notifyError(
      `Erreur lors de la demande de contact pour l'accord ${props.accord.name}`,
    )
  } finally {
    showcaseLoading.value = false
  }
}
</script>
