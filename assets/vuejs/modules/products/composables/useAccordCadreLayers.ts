import { ref, type Ref } from 'vue'

export interface AccordCadreLayersComposable {
  showMoreInformationsLayer: Ref<boolean>
  showConfirmationLayer: Ref<boolean>
  showSuccessLayer: Ref<boolean>
  showNegociatedTermsLayer: Ref<boolean>
  showFatInterestModal: Ref<boolean>
  openMoreInformationsLayer: () => void
  openConfirmationLayer: () => void
  openSuccessLayer: () => void
  openNegociatedTermsLayer: () => void
  openFatInterestModal: () => void
  closeAllLayers: () => void
}

export function useAccordCadreLayers(): AccordCadreLayersComposable {
  const showMoreInformationsLayer = ref(false)
  const showConfirmationLayer = ref(false)
  const showSuccessLayer = ref(false)
  const showNegociatedTermsLayer = ref(false)
  const showFatInterestModal = ref(false)

  // Actions
  const openMoreInformationsLayer = () => {
    showMoreInformationsLayer.value = true
  }

  const openConfirmationLayer = () => {
    showConfirmationLayer.value = true
  }

  const openSuccessLayer = () => {
    showSuccessLayer.value = true
  }

  const openNegociatedTermsLayer = () => {
    showNegociatedTermsLayer.value = true
  }

  const openFatInterestModal = () => {
    showFatInterestModal.value = true
  }

  const closeAllLayers = () => {
    showMoreInformationsLayer.value = false
    showConfirmationLayer.value = false
    showSuccessLayer.value = false
    showNegociatedTermsLayer.value = false
    showFatInterestModal.value = false
  }

  return {
    // States
    showMoreInformationsLayer,
    showConfirmationLayer,
    showSuccessLayer,
    showNegociatedTermsLayer,
    showFatInterestModal,
    // Actions
    openMoreInformationsLayer,
    openConfirmationLayer,
    openSuccessLayer,
    openNegociatedTermsLayer,
    openFatInterestModal,
    closeAllLayers,
  }
}
