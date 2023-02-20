import BaseClientService from '@/vuejs/services/BaseClientService'
import { AccordCadre } from '@/vuejs/types/AccordCadre'

export default class AccordCadreHttpClient extends BaseClientService {
  public findAccordsCadresByParams<T extends []>(params): Promise<T> {
    return this.apiClient
      .postForm<T>(
        'accords-cadre',
        params
      )
      .then((response) => response.data)
  }

  public getAccordCadre<T extends []>(id: number): Promise<AccordCadre> {
    return this.apiClient
        .get(`accord-cadre/${id}`)
        .then((response) => response.data)
  }

  public updateAccountAccordsCadresByParams<T extends []>(params): Promise<T> {
    return this.apiClient
      .put<T>(`account_accord_cadres/${params.id}`,  params)
      .then((response) => response.data)
  }
}
