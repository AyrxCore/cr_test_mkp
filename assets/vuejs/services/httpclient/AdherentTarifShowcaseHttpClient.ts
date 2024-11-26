import BaseClientService from '@/vuejs/services/BaseClientService'

export default class AdherentTarifShowcaseHttpClient extends BaseClientService {
  public requestContactForShowcase<T>(
    showcaseId: string,
    accordName: string,
  ): Promise<T> {
    return this.apiClient
      .patch(`adherent_tarif_showcases/${showcaseId}/request-contact`, {
        accordName,
      })
      .then((response) => response.data)
  }
}
