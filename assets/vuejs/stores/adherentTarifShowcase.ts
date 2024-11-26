import { defineStore } from 'pinia'
import AdherentTarifShowcaseHttpClient from '@/vuejs/services/httpclient/AdherentTarifShowcaseHttpClient'
import { AdherentTarifShowcase } from '../types/AdherentTarifShowcase'
import { useUserStore } from '@/vuejs/stores/user'

export const useAdherentTarifShowcaseStore = defineStore(
  'adherentTarifShowcase',
  {
    actions: {
      async handleRequestContactForShowcase(
        showcaseId: string,
        accordName: string,
        accordId: string,
      ) {
        const userStore = useUserStore()
        const adherentTarifShowcases =
          userStore.user?.account?.adherent?.adherentTarifShowcases
        const res = (await AdherentTarifShowcaseHttpClient.get(
          true,
        ).requestContactForShowcase(
          showcaseId,
          accordName,
        )) as AdherentTarifShowcase
        if (res && res.contactRequested) {
          this.updateContactRequested(accordId, adherentTarifShowcases)
        }
      },
      updateContactRequested(
        accordId: string,
        adherentTarifShowcases: AdherentTarifShowcase[] | [],
      ) {
        const showcase = adherentTarifShowcases.find(
          (showcase) => showcase.accordId === accordId,
        )
        if (showcase) {
          showcase.contactRequested = true
          this.user = { ...this.user }
        }
      },
    },
  },
)
