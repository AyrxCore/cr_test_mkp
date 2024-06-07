import BaseClientService from '@/vuejs/services/BaseClientService'

import { CompanyMandate } from '@/vuejs/types/Cart'

export default class CompanyHttpClient extends BaseClientService {
  public getExistingMandates<T extends CompanyMandate[]>(): Promise<T> {
    return this.apiClient
      .get(`company/mandates`)
      .then((response) => response.data)
  }
}
