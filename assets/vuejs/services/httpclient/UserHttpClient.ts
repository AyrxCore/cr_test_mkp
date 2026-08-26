import BaseClientService from '@/vuejs/services/BaseClientService'
import {
  AuthenticateResponse,
  AuthenticateUserData,
  PasswordChangeRequest,
  User,
} from '@/vuejs/types/User'
import {
  Account,
  AccountEmail,
  DefaultBillingAddressToUpdate,
  DefaultShippingAddressToUpdate,
  SubAccountData,
} from '@/vuejs/types/Account'

export default class UserHttpClient extends BaseClientService {
  public getUserToken<T extends AuthenticateResponse>(
    userDatas: AuthenticateUserData,
  ): Promise<T> {
    return this.apiClient
      .post<T>('authentication/token', {
        username: userDatas.username,
        password: userDatas.password,
      })
      .then((response) => response.data)
  }

  public async getUserAccounts(): Promise<Account[]> {
    const { data: accounts } = await this.apiClient.get<Account[]>('accounts')

    return accounts
  }

  public async selectUserAccount(id: string): Promise<unknown> {
    const response = await this.apiClient.get(`accounts/${id}/select`)
    return response.data
  }

  public getUserMe<T extends User>(): Promise<T> {
    return this.apiClient.get('me').then((response) => response.data)
  }

  public updateUserAddress<T extends User>(
    address: DefaultBillingAddressToUpdate | DefaultShippingAddressToUpdate,
  ): Promise<T> {
    return this.apiClient
      .patch(`sub_accounts/${address.id}`, address)
      .then((response) => response.data)
  }

  public updateUserAccountEmail(
    accountEmail: AccountEmail,
  ): Promise<unknown | { err: unknown }> {
    return this.apiClient
      .patch<unknown>(`sub_accounts/${accountEmail.id}`, accountEmail)
      .then((response) => {
        return response.data
      })
      .catch((res) => {
        return { err: res.response.data }
      })
  }

  public updateUserAccountDetails(subAccount: SubAccountData & { accountId: string }): Promise<SubAccountData> {
    return this.apiClient
      .patch(`sub_accounts/${subAccount.id}`, subAccount)
      .then((response) => response.data)
  }

  public updateUserPassword<T extends User>(
    data: PasswordChangeRequest,
  ): Promise<T> {
    return this.apiClient
      .patch('user/change-password', data)
      .then((response) => response.data)
  }

  public logout<T extends User>(): Promise<T> {
    return this.apiClient.get('user/logout').then((response) => response.data)
  }
}
