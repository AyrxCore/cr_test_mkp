export interface BreadcrumbItem {
  id: number
  name: string
  url: {
    name: string
    query: {
      category: number
    }
  }
}
