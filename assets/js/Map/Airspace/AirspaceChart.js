import AirspaceChartSettings from './AirspaceChartSettings'
import LabelBounds from './LabelBounds'
import Padding from './Padding'

export default class AirspaceChart {
  constructor() {
    this.data = []
    this.canvas = document.getElementById('airspace-canvas')
    this.canvas.width = Math.max(window.innerWidth * 0.8, 992)
    this.padding = new Padding(
      AirspaceChartSettings.CANVAS_PADDING_TOP,
      AirspaceChartSettings.CANVAS_PADDING_RIGHT,
      AirspaceChartSettings.CANVAS_PADDING_BOTTOM,
      AirspaceChartSettings.CANVAS_PADDING_LEFT
    )
    window.addEventListener('resize', this._resizeCanvas.bind(this), false)
  }
  update(data) {
    this.data = data
    this._draw()
  }
  loading(loading) {
    const loader = this.canvas.parentElement.querySelector('.progress')
    if (loading) {
      loader.style.display = 'block'
    } else {
      loader.style.display = 'none'
    }
  }

  _draw() {
    const ctx = this.canvas.getContext('2d')
    ctx.clearRect(0, 0, this.canvas.width, this.canvas.height)
    this._drawAirspace(ctx, this.padding)
    this._drawTerrain(ctx, this.padding)
    this._drawAxes(ctx, this.padding)
    this._drawAirspaceLabels(ctx, this.padding)
  }

  _drawAirspace(ctx, padding) {
    const usableWidth = this.canvas.width - padding.horizontal
    const usableHeight = this.canvas.height - padding.vertical

    this.data.airspace.forEach((as) => {
      as['horizontalBounds'].forEach((bounds) => {
        const lowerBound = as.verticalBounds.lower
        const upperBound = as.verticalBounds.upperDatum.startsWith('FL') ? 6000 : as.verticalBounds.upper
        const height = upperBound - lowerBound

        const x = usableWidth * bounds.in + padding.left
        const y = usableHeight - (usableHeight / 6000 * lowerBound) - (usableHeight / 6000 * height) + padding.top
        const h = usableHeight / 6000 * height

        const color = AirspaceChartSettings.getColorFor(as.type)
        ctx.fillStyle = color
        ctx.strokeStyle = color.replace(/[^,]+(?=\))/, '1')
        ctx.lineWidth = 2

        if (as.verticalBounds.lowerDatum === 'GND') {
          const terrain = this.data.terrain.filter((t) => {
            return t.relativeDistance >= bounds.in && t.relativeDistance <= bounds.out
          })
          ctx.moveTo(x, usableHeight - (usableHeight / 6000 * terrain[0].elevation) + padding.top)
          ctx.beginPath()
          // spodek
          terrain.forEach((t) => {
            const x = usableWidth * t.relativeDistance + padding.left
            const y = padding.top + usableHeight - (usableHeight / 6000 * t.elevation) - (usableHeight / 6000 * lowerBound)
            ctx.lineTo(x, y)
          })
          // vrch
          terrain.reverse().forEach((t) => {
            if (as.verticalBounds.upperDatum === 'GND') {
              const x = usableWidth * t.relativeDistance + padding.left
              const y = padding.top + usableHeight - (usableHeight / 6000 * t.elevation) - (usableHeight / 6000 * height)
              ctx.lineTo(x, y)
            } else {
              const x = usableWidth * t.relativeDistance + padding.left
              const y = padding.top + usableHeight - (usableHeight / 6000 * height) - (usableHeight / 6000 * lowerBound)
              ctx.lineTo(x, y)
            }
          })
          ctx.closePath()
          ctx.fill()
          ctx.stroke()
        } else {
          const w = usableWidth * (bounds.out - bounds.in)
          ctx.fillRect(x, y, w, h)
          ctx.strokeRect(x, y, w, h)
        }
      })
    })
  }

  _drawTerrain(ctx, padding) {
    const usableWidth = this.canvas.width - padding.horizontal
    const usableHeight = this.canvas.height - padding.vertical

    ctx.moveTo(padding.left + usableWidth, padding.top + usableHeight)
    ctx.beginPath()
    ctx.lineTo(padding.left, padding.top + usableHeight)
    ctx.lineWidth = 2
    ctx.strokeStyle = '#000000'
    ctx.fillStyle = '#525252'

    this.data.terrain.forEach((t) => {
      const elevation = t.elevation
      const x = usableWidth * t.relativeDistance + padding.left
      const y = usableHeight - (usableHeight / 6000 * elevation) + padding.top
      ctx.lineTo(x, y)
    })
    ctx.lineTo(padding.left + usableWidth, padding.top + usableHeight)
    ctx.closePath()
    ctx.fill()
    ctx.stroke()
  }

  _drawAxes(ctx, padding) {
    const usableWidth = this.canvas.width - padding.horizontal
    const usableHeight = this.canvas.height - padding.vertical
    const step = usableHeight / 6
    ctx.fillStyle = '#000000'
    ctx.font = AirspaceChartSettings.CANVAS_FONT
    ctx.textBaseline = 'middle'
    ctx.textAlign = 'right'
    ctx.fillText('AMSL', padding.left + 15, padding.top - 20)
    for (let i = 0; i <= 6; i++) {
      // Axis labels
      ctx.fillText(AirspaceChartSettings.Y_AXIS_LABELS[i], padding.left - 12, usableHeight + padding.top - (step * i))
      ctx.beginPath()
      ctx.moveTo(padding.left - 10, usableHeight + padding.top - (step * i))
      ctx.lineWidth = 2
      ctx.strokeStyle = '#000000'
      ctx.lineTo(padding.left, usableHeight + padding.top - (step * i))
      ctx.stroke()
      // grid
      ctx.beginPath()
      ctx.lineWidth = 0.1
      ctx.strokeStyle = '#000000'
      ctx.moveTo(padding.left, usableHeight + padding.top - (step * i))
      ctx.lineTo(usableWidth + padding.left, usableHeight + padding.top - (step * i))
      ctx.stroke()
    }
    // X
    ctx.beginPath()
    ctx.lineWidth = 2
    ctx.strokeStyle = '#000000'
    ctx.moveTo(padding.left, this.canvas.height - padding.bottom)
    ctx.lineTo(this.canvas.width - padding.right, this.canvas.height - padding.bottom)
    ctx.stroke()
    // Y
    ctx.moveTo(padding.left, this.canvas.height - padding.bottom)
    ctx.lineTo(padding.left, padding.top)
    ctx.stroke()
  }

  _drawAirspaceLabels(ctx, padding) {
    const usableWidth = this.canvas.width - padding.horizontal
    const usableHeight = this.canvas.height - padding.vertical
    const airspaceLabels = []

    this.data.airspace.forEach((as) => {
      as['horizontalBounds'].forEach((bounds) => {
        const lowerBound = as.verticalBounds.lower
        const upperBound = as.verticalBounds.upperDatum.startsWith('FL') ? 6000 : as.verticalBounds.upper
        const height = upperBound - lowerBound
        const y = usableHeight - (usableHeight / 6000 * lowerBound) - (usableHeight / 6000 * height) + padding.top

        const labelText = `${as.type} ${as.name}`
        const labelX = padding.left + (usableWidth * bounds.in + usableWidth * bounds.out) / 2
        const labelY = y + 2
        const labelBounds = new LabelBounds(
          labelX,
          ctx.measureText(labelText).width + labelX, labelY,
          AirspaceChartSettings.CANVAS_FONT_SIZE + labelY
        )
        const collision = airspaceLabels.filter((label) => label.intersects(labelBounds))

        ctx.fillStyle = '#000000'
        ctx.font = AirspaceChartSettings.CANVAS_FONT
        ctx.textBaseline = 'top'
        ctx.textAlign = 'center'
        ctx.fillText(labelText, labelX, collision ? labelY + collision.length * 14 : labelY)

        airspaceLabels.push(labelBounds)
      })
    })
  }

  _resizeCanvas() {
    this.canvas.width = Math.max(window.innerWidth * 0.8, 992)
    this._draw()
  }
}
