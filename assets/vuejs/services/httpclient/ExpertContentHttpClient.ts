import BaseClientService from '@/vuejs/services/BaseClientService'
// import { DynamicEntity } from '@/vuejs/types/DynamicEntity'
import {
  ExpertContent,
  ExpertContentCategory,
} from '@/vuejs/types/ExpertContent'

export type ExpertContentPropertyParams = {
  property_id: number
  value: string
}

export const ExpertsContentsParams = {
  properties: [] as ExpertContentPropertyParams[],
  categories: [],
  cache_key: String,
}

export default class ExpertContentHttpClient extends BaseClientService {
  public findExpertsContentsCategories<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>(`experts_contents_categories`)
      .then((response) => response.data)
  }

  public findExpertsContentsByParams<T extends []>(params): Promise<T> {
    return this.apiClient
      .postForm<T>(`experts_contents/${params.cache_key}`, {
        categories: params.categories,
        properties: params.properties,
      })
      .then((response) => response.data)
  }

  public findExpertsContents<T extends []>(): Promise<T> {
    return this.apiClient.get<T>(`experts_contents`).then((response) => {
      return response.data
    })
  }

  public getExpertContent<T extends []>(slug: string): Promise<ExpertContent> {
    return this.apiClient
      .get(`experts_contents?slug=${slug}`)
      .then((response) => {
        return response.data.length > 0 ? response.data[0] : null
      })
  }

  public getBanner<T extends []>(): Promise<T> {
    return this.apiClient.get<T>('banner/1').then((response) => {
      return response.data
    })
  }
}
