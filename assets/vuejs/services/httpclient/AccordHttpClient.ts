import BaseClientService from '@/vuejs/services/BaseClientService'
import { AccordApiResponse } from '@/vuejs/types/Accord'

export default class AccordHttpClient extends BaseClientService {
  public fetchAccordWithStores(accordId: string): Promise<AccordApiResponse> {
    return this.apiClient
      .get<AccordApiResponse>(`accords/${accordId}`)
      .then((response) => response.data)
  }
}
