// décrit une énumération des types possibles
export enum AlertType {
  info = 0,
  success = 1,
  warning = 2,
  danger = 3,
}

// Décrit le state général du store alert
export interface AlertStoreState {
  show: boolean
  message: string
  type: AlertType
}
