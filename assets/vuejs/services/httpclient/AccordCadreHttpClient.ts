import BaseClientService from '@/vuejs/services/BaseClientService'
import { AccordCadre } from '@/vuejs/types/AccordCadre'

export default class AccordCadreHttpClient extends BaseClientService {
  public findAccordsCadresByParams<T extends []>(params): Promise<T> {
    return this.apiClient
      .postForm<T>(
        'accords-cadre',
        {
              properties: params.properties,
            }
      )
      .then((response) => response.data)
  }

  public getAccordCadre<T extends []>(id: number): Promise<AccordCadre> {
    return this.apiClient
        .get(`accord-cadre/${id}`)
        .then((response) => response.data)
  }
}
