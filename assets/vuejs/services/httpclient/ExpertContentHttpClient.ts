import BaseClientService from '@/vuejs/services/BaseClientService'
import { ExpertContent } from '@/vuejs/types/ExpertContent'

export default class ExpertContentHttpClient extends BaseClientService {
  public findExpertsContentsCategories<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('expert-content-categories')
      .then((response) => response.data['hydra:member'])
  }

  public findExpertsContents<T extends []>(): Promise<T> {
    return this.apiClient.get<T>('expert-contents').then((response) => {
      return response.data['hydra:member']
    })
  }

  public getExpertContent<T extends []>(slug: string): Promise<ExpertContent> {
    return this.apiClient.get(`expert-contents/${slug}`).then((response) => {
      return response.data
    })
  }
}
