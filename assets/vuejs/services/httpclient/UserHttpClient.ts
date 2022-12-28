import BaseClientService from '@/vuejs/services/BaseClientService'
import {
  AuthenticateResponse,
  AuthenticateUserDatas, PasswordChangeRequest,
  User,
} from '@/vuejs/types/User'
import {
  AccountDetails,
  AccountEmail,
  DefaultBillingAddressToUpdate,
  DefaultShippingAddressToUpdate,
} from '@/vuejs/types/Account'

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

  public getUserAccounts<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('user/accounts')
      .then((response) => response.data)
  }

  public selectUserAccount<T extends []>(id: string): Promise<T> {
    return this.apiClient
      .get<T>(`user/account/${id}/select`)
      .then((response) => response.data)
  }

  public getUserMe<T extends User>(): Promise<T> {
    return this.apiClient.get('user/me').then((response) => response.data)
  }

  public updateUserAddress<T extends User>(
    adress: DefaultBillingAddressToUpdate | DefaultShippingAddressToUpdate,
  ): Promise<T> {
    return this.apiClient
      .patch(`sub_accounts/${adress.id}`, adress)
      .then((response) => response.data)
  }

  public updateUserAccountEmail<T extends User>(
   accountEmail: AccountEmail
  ): Promise<T> {
    return this.apiClient
      .patch(`sub_accounts/${accountEmail.id}`, accountEmail)
      .then((response) => response.data)
  }

  public updateUserPassword<T extends User>(
      datas: PasswordChangeRequest
  ): Promise<T> {
    return this.apiClient
        .patch(`user/change-password`, datas)
        .then((response) => response.data)
  }

  public logout<T extends User>(): Promise<T> {
    return this.apiClient.get('user/logout').then((response) => response.data)
  }
}
