import { defineStore } from 'pinia'
import { notifyError } from '@/vuejs/services/utils'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'
import { AccordCadreState } from '@/vuejs/types/AccordCadre.ts'
import { AccountAccordCadreStatus } from '@/vuejs/types/AccountAccordCadre'
import { ACCORD_CADRE_TYPE } from '@/vuejs/services/const.ts'

export const useAccordCadreStore = defineStore('accordCadre', {
  state: (): AccordCadreState => ({
    accordCadre: null,
    errorLoading: null,
  }),

  actions: {
    async findAccordCadreById(id) {
      this.errorLoading = null
      try {
        this.accordCadre = await ProductHttpClient.get().findProductById(id)
      } catch (error) {
        this.errorLoading = true
      }
    },

    async attachAccordCadre() {
      if (!this.accordCadre?.accountAccordCadre) {
        notifyError('Aucun accord-cadre trouvé. Veuillez actualiser la page.')
        return false
      }

      const currentStatus = this.accordCadre.accountAccordCadre

      if (
        currentStatus.status === AccountAccordCadreStatus.PENDING ||
        currentStatus.status === AccountAccordCadreStatus.ACTIVATED
      ) {
        return false
      }

      try {
        await ProductHttpClient.get().updateAccountAccordsCadresByParams({
          accordId: currentStatus.accordId,
          accordName: this.accordCadre?.accordCadreContent?.name,
        })

        // Mise à jour locale du status en PENDING
        this.accordCadre.accountAccordCadre.status =
          AccountAccordCadreStatus.PENDING

        return true
      } catch (error) {
        notifyError(
          'Une erreur est survenue lors du rattachement. Veuillez réessayer ou contacter le support.',
        )
        return false
      }
    },
  },
  getters: {
    listBlocks: (state) => {
      return state.accordCadre?.accordCadreContent?.listBlocks
    },
    bannerBlockContent: (state) => {
      return state.accordCadre?.accordCadreContent?.listBlocks?.bannerBlock
    },
    negociatedTermsBlockContent: (state) => {
      return state.accordCadre?.accordCadreContent?.listBlocks
        ?.negociatedTermsBlock
    },
    presentationBlockContent: (state) => {
      return state.accordCadre?.accordCadreContent?.listBlocks
        ?.presentationBlock
    },
    stepsBlockContent: (state) => {
      return state.accordCadre?.accordCadreContent?.listBlocks?.stepsBlock
    },
    showStepsBlock: (state): boolean => {
      const type = state.accordCadre?.accordCadreContent?.type
      const status =
        type === ACCORD_CADRE_TYPE.DIRECT
          ? AccountAccordCadreStatus.ACTIVATED
          : state.accordCadre?.accountAccordCadre?.status
      return (
        status === AccountAccordCadreStatus.PENDING ||
        status === AccountAccordCadreStatus.ACTIVATED
      )
    },
    contactForm: (state) => {
      return state.accordCadre?.accordCadreContent?.contactForm ?? false
    },
    labelStatus: (state) => {
      const type = state.accordCadre?.accordCadreContent?.type
      const status =
        type === ACCORD_CADRE_TYPE.DIRECT
          ? AccountAccordCadreStatus.ACTIVATED
          : state.accordCadre?.accountAccordCadre?.status

      const content = state.accordCadre?.accordCadreContent

      let label: string

      switch (status) {
        case AccountAccordCadreStatus.ACTIVATED:
          label = content?.labelActivated ?? ''
          break
        case AccountAccordCadreStatus.PENDING:
          label = content?.labelPending ?? ''
          break
        default:
          label = content?.labelNotActivated ?? 'À activer'
          break
      }

      return { status, label }
    },
  },
})
