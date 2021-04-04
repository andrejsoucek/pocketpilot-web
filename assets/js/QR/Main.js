import Main from '../Main'
import qrcode from 'qrcode-generator-es6'

function start(win) {
  const main = new Main(win)

  return {
    requireQR: () => {
      const selector = '.qr-code'
      const apply = (el) => {
        const qr = new qrcode(0, 'M') // eslint-disable-line
        const selectors = el.dataset.qrValues
        const data = []
        selectors.split(',').forEach(selector => {
          const el = document.querySelector(selector)
          if (el) {
            data.push(el.value)
          }
        })
        qr.addData(data.join(';'))
        qr.make()
        el.innerHTML = qr.createSvgTag({})
      }
      main.attach(selector, apply)
    }
  }
}

export const qr = start(window)
