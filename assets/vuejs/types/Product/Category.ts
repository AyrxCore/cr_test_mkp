export interface Category {
  id: string
  externalId: string
  name: string
  parentId: string | undefined
  children: Array<Category>
}
