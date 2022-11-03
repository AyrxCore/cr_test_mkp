import BaseClientService from '@/vuejs/services/BaseClientService'
import { AuthenticateResponse, AuthenticateUserDatas } from '@/vuejs/types/User'

export default class UserHttpClient extends BaseClientService {
  public getUserToken<T extends AuthenticateResponse>(
    userDatas: AuthenticateUserDatas,
  ): Promise<T> {
    return this.apiClient
      .post<T>('authentication/token', {
        email: userDatas.email,
        password: userDatas.password,
      })
      .then((response) => response.data)
  }
}
