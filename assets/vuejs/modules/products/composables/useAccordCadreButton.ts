import { computed } from 'vue'
import { storeToRefs } from 'pinia'

import router from '@/vuejs/router'
import { useAccordCadreStore } from '@/vuejs/stores/accordCadre'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { ACCORD_CADRE_TYPE } from '@/vuejs/services/const.ts'
import { AccountAccordCadreStatus } from '@/vuejs/types/AccountAccordCadre'
import type { AccordCadreLayersComposable } from '@/vuejs/modules/products/composables/useAccordCadreLayers'

export function useAccordCadreButton(layers: AccordCadreLayersComposable) {
  const accordCadreStore = useAccordCadreStore()
  const { accordCadre, contactForm } = storeToRefs(accordCadreStore)

  const accountStatus = computed<string | undefined>(() => {
    const type = accordCadre.value?.accordCadreContent?.type
    return type === ACCORD_CADRE_TYPE.DIRECT
      ? AccountAccordCadreStatus.ACTIVATED
      : accordCadre.value?.accountAccordCadre?.status
  })

  const urlCtaRattachement = computed<string>(() => {
    return accordCadre.value?.accordCadreContent?.urlCtaRattachement || ''
  })

  const labelCtaRattachement = computed<string>(() => {
    const status = accountStatus.value
    const type = accordCadre.value?.accordCadreContent?.type
    const label =
      accordCadre.value?.accordCadreContent?.labelCtaRattachement || ''

    if (status === AccountAccordCadreStatus.NOT_ACTIVATED) {
      return type === ACCORD_CADRE_TYPE.BONUUS ? "Demander l'accès" : label
    }

    if (status === AccountAccordCadreStatus.PENDING) {
      return type === ACCORD_CADRE_TYPE.DIRECT ? label : ''
    }

    if (status === AccountAccordCadreStatus.ACTIVATED) {
      return type === ACCORD_CADRE_TYPE.STANDARD ? '' : label
    }
  })

  const shouldShowButton = computed<boolean>(() => {
    const status = accountStatus.value
    const type = accordCadre.value?.accordCadreContent?.type

    if (type === ACCORD_CADRE_TYPE.DIRECT && !labelCtaRattachement.value) {
      return false
    }
    if (status === AccountAccordCadreStatus.PENDING) {
      return type === ACCORD_CADRE_TYPE.DIRECT
    }

    if (status === AccountAccordCadreStatus.ACTIVATED) {
      return type === ACCORD_CADRE_TYPE.DIRECT ||
        type === ACCORD_CADRE_TYPE.BONUUS
        ? !!urlCtaRattachement.value
        : false
    }

    return true
  })

  const isActivatedWithUrl = computed<boolean>(() => {
    return (
      accountStatus.value === AccountAccordCadreStatus.ACTIVATED &&
      !!urlCtaRattachement.value
    )
  })

  const shouldShowContactFormButton = computed<boolean>(() => {
    return (
      !!contactForm.value &&
      (accountStatus.value === AccountAccordCadreStatus.ACTIVATED ||
        accountStatus.value === AccountAccordCadreStatus.PENDING)
    )
  })

  const handleButtonClick = () => {
    const status = accountStatus.value

    sendGtmEvent('fat_cta_rattachement_click', {
      link_url: urlCtaRattachement.value,
      origin_url: router.currentRoute.value.fullPath,
    })

    if (
      status === AccountAccordCadreStatus.ACTIVATED &&
      urlCtaRattachement.value
    ) {
      window.open(urlCtaRattachement.value, '_blank', 'noopener,noreferrer')
    } else {
      layers.openConfirmationLayer()
    }
  }

  return {
    accountStatus,
    urlCtaRattachement,
    shouldShowButton,
    isActivatedWithUrl,
    shouldShowContactFormButton,
    handleButtonClick,
    labelCtaRattachement,
  }
}
