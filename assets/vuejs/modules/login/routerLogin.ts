import { RouteRecordRaw } from 'vue-router'
import LoginForm from '@/vuejs/modules/login/views/InternalLoginForm.vue'
import LoginPage from '@/vuejs/modules/login/pages/LoginPage.vue'

export enum LoginPageList {
  LOGIN_BASE = 'login-base',
  LOGIN_AUTH = 'login',
}

export const routes: RouteRecordRaw[] = [
  {
    path: '/app',
    component: LoginPage,
    name: LoginPageList.LOGIN_BASE,
    children: [
      {
        path: LoginPageList.LOGIN_AUTH,
        component: LoginForm,
        name: LoginPageList.LOGIN_AUTH,
      },
    ],
  },
]
