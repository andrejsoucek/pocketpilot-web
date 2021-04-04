export default class Main {
  constructor(win, snippetPostProcessor = null) {
    this.win = win
    this.snippetPostProcessor = snippetPostProcessor
  }
  attach(selector, decorator) {
    const apply = (document) => {
      document.querySelectorAll(selector).forEach(el => {
        decorator(el)
      })
      if (this.snippetPostProcessor) {
        this.snippetPostProcessor.register(selector, decorator)
      }
    }
    apply(this.win.document)
  }
}
