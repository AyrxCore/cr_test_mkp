import { Account } from '@/vuejs/types/Account'
import { ExternalApiDataEntity } from '@/vuejs/types/ExternalApiDataEntity'

export interface User extends ExternalApiDataEntity {
  id: string
  email: string
  roles: string[]
  account: Account
  username: string
  firstName: string
  lastName: string
  editingInfo: string[]
  userInfoUpdateRequests: string[]
}

export interface UserLocation {
  lat: number
  lng: number
  timestamp: number
  error: string | null
}

export interface UserStoreState {
  user: User
  editingInfo: string[]
  isNeoAutoLogin: boolean
  userLocation: UserLocation | null
}

export interface AuthenticateUserData {
  username: string
  password: string
}

export interface AuthenticateResponse {
  token: string
}

export enum LoginResponse {
  UserDisabled = 'user_disabled',
  UserEmptyAccount = 'user_empty_account',
  InvalidCredentials = 'Identifiants invalides.',
}

export interface PasswordChangeRequest {
  password: string
  confirmation: string
  currentPassword: string
}
