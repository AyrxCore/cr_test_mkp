import BaseClientService from '@/vuejs/services/BaseClientService'
import { Banner } from '@/vuejs/types/Banner'

export default class BannerHttpClient extends BaseClientService {
  public getBanner(): Promise<Banner> {
    return this.apiClient.get('banners/1').then((response) => {
      return response.data
    })
  }
}
