export default class Padding {
  constructor(top, right, bottom, left) {
    this._top = top
    this._right = right
    this._bottom = bottom
    this._left = left
  }

  get top() {
    return this._top
  }

  get right() {
    return this._right
  }

  get bottom() {
    return this._bottom
  }

  get left() {
    return this._left
  }

  get vertical() {
    return this._top + this._bottom
  }

  get horizontal() {
    return this._left + this._right
  }
}
