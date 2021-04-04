export default class LabelBounds {
  constructor(minX, maxX, minY, maxY) {
    this.minX = minX
    this.maxX = maxX
    this.minY = minY
    this.maxY = maxY
  }

  intersects(bounds) {
    const minX = this.minX
    const maxX = this.maxX
    const min2X = bounds.minX
    const max2X = bounds.maxX
    const minY = this.minY
    const maxY = this.maxY
    const min2Y = bounds.minY
    const max2Y = bounds.maxY

    const intX = max2X >= minX && min2X <= maxX
    const intY = max2Y >= minY && min2Y <= maxY

    return intX && intY
  }
}
