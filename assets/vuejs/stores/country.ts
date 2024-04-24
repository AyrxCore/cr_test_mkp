import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import { CountryStoreState } from '@/vuejs/types/Country'
import CountryHttpClient from '@/vuejs/services/httpclient/CountryHttpClient'
import { SelectOption } from '@/vuejs/types/Select'

export const useCountryStore = defineStore({
  id: 'country',
  state: (): CountryStoreState => ({
    countries: [],
  }),

  actions: {
    async getCountries(): Promise<void> {
      const alertStore = useAlertStore()
      try {
        this.countries = await CountryHttpClient.get().getCountriesAsBuyer()
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    getCountriesForSelect(): SelectOption[] {
      return this.countries.map((country) => {
        return {
          label: country.name,
          value: country.id,
        }
      })
    },
  },
})
