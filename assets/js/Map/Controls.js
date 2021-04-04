import $ from '../DOMLoaded'

export default class Controls {
  constructor(track) {
    this.track = track
    this._bind()
  }

  _bind() {
    $(() => {
      const form = document.querySelector('.save-track > form')
      const control = document.querySelector('.controls-buttons > .save')
      const hidden = form.querySelector('input[name="waypoints"]')
      control.addEventListener('click', () => {
        hidden.value = JSON.stringify(this.track.getWaypoints().map((wp) => wp.getLatLng()))
      })
    })
  }
}
