export function getImage(urlImage: string): string {
  return new URL(urlImage, import.meta.url).href
}

export function animateSubMenu(menuToggle :string, overlay, button, menu): void {
  if(menuToggle === 'open') {
    overlay.classList.add('open')
    menu.classList.add('open')
    button.classList.add('on')
  }

  if(menuToggle === 'close') {
    button.classList.remove('on')
    overlay.classList.remove('open')
    menu.classList.remove('open')
  }
}
