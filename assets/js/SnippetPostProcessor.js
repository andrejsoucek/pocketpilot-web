import initIcons from './Common/icons'

export default class SnippetPostProcessor {
  constructor() {
    this.decorators = []
  }
  register(selector, decorator) {
    this.decorators.push(
      { selector: selector, decorator: decorator }
    )
  }
  applyCallbacksOn(dom) {
    initIcons(dom)
    this.decorators.forEach((cb) => {
      const matches = [dom, ...dom.querySelectorAll(cb.selector)].filter(el => el.matches(cb.selector))
      matches.forEach(el => {
        cb.decorator(el)
      })
    })
  }
}
