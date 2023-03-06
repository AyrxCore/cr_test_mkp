
import { defineStore } from 'pinia'
import { useUserStore } from '@/vuejs/stores/user'
import { Contact } from '@/vuejs/types/Contact'
import ContactHttpClient from '@/vuejs/services/httpclient/ContactHttpClient'

export interface ContactStoreState {
  contact?: Contact,
  motifs?: [],
}

export const useContactStore = defineStore({
  id: 'contact',
  state: (): ContactStoreState => ({
    contact: {
      lastName: null,
      firstName: null,
      email: null,
      description: null,
      phone: null,
      motif: null,
      companyName: null,
      accordCadreName: null,
      _token: null,
    },
    motifs: [],
  }),

  actions: {
    async init(): Promise<Contact> {
      try {
        if (this.motifs.length === 0) {
          this.motifs = await ContactHttpClient.get().getMotifs()
        }
        this.contact._token = await ContactHttpClient.get().getToken()
        const userStore = useUserStore()
        if (userStore.getUser) {
          this.contact.lastName = userStore.getUser.lastName
          this.contact.firstName = userStore.getUser.firstName
          this.contact.email = userStore.getUser.email
          this.contact.companyName = userStore.getUser.account.buyer.name
        }
        this.contact.motif = ''
        this.contact.description = ''
        this.contact.phone = ''
        this.contact.accordCadreName = ''
      } catch (error) {
        return null
      }
    },
    async sendEmail(contact: Contact): Promise<Array<string>> {
      return await ContactHttpClient.get().sendEmail(contact)
    },
  },
})
