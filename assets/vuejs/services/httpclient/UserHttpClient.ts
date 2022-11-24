import BaseClientService from '@/vuejs/services/BaseClientService'
import {
  AuthenticateResponse,
  AuthenticateUserDatas,
  User,
} from '@/vuejs/types/User'

export default class UserHttpClient extends BaseClientService {
  public getUserToken<T extends AuthenticateResponse>(
    userDatas: AuthenticateUserDatas,
  ): Promise<T> {
    return this.apiClient
      .post<T>('authentication/token', {
        username: userDatas.username,
        password: userDatas.password,
      })
      .then((response) => response.data)
  }

  public getUserMe<T extends User>(): Promise<T> {
    return this.apiClient.get('user/me').then((response) => response.data)
  }

  public logout<T extends User>(): Promise<T> {
    return this.apiClient.get('user/logout').then((response) => response.data)
  }
}
