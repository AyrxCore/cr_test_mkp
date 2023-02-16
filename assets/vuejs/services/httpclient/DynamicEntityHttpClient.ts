import BaseClientService from '@/vuejs/services/BaseClientService'
import { DynamicEntity } from '@/vuejs/types/DynamicEntity'

export type DynamicEntityPropertyParams = {
  property_id: number
  value: string
}

export const DynamicsEntitiesParams = {
  properties: [] as DynamicEntityPropertyParams[],
  categories: [],
  cache_key: String,
}

export default class DynamicEntityHttpClient extends BaseClientService {
  public findDynamicsEntitiesByParams<T extends []>(params): Promise<T> {
    return this.apiClient
      .postForm<T>(
        `experts_contents/${params.cache_key}`,
        {
              categories: params.categories,
              properties: params.properties,
            }
      )
      .then((response) => response.data)
  }

  public findDynamicsEntities<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>(
        `experts_contents`
      )
      .then((response) => response.data)
  }

  public getDynamicEntity<T extends []>(id: number): Promise<DynamicEntity> {
    return this.apiClient
        .get(`expert_content/${id}`)
        .then((response) => response.data)
  }
}
