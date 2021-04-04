import $ from '../DOMLoaded'
import { DomUtil } from 'leaflet'
import { getHeading } from './Utils'

export default class TrackTable {
  constructor() {
    this.table = this.renderEmpty()
    this._bind()
  }
  renderEmpty() {
    const wrapper = document.getElementById('waypoints').querySelector('.info-table-wrapper')
    const table = DomUtil.create('table', 'info-table waypoints')
    wrapper.insertBefore(table, wrapper.children[0])
    const header = DomUtil.create('tr', 'info-table-header', table)
    DomUtil.create('th', '', header).innerText = 'WPT'
    DomUtil.create('th', '', header).innerText = 'HDG'
    DomUtil.create('th', '', header).innerText = 'DIST'
    DomUtil.create('th', '', header).innerText = 'TIME'
    const total = DomUtil.create('tr', 'info-table-summary', table)
    DomUtil.create('td', '', total).innerHTML = '<strong>SUM</strong>'
    DomUtil.create('td', '', total)
    DomUtil.create('td', '', total).innerText = '-'
    DomUtil.create('td', '', total).innerText = '-'
    return table
  }
  addWaypoint(index, current, created, next) {
    const row = DomUtil.create('tr', null)
    const place = DomUtil.create('td', null, row)
    place.innerText = 'Loading...'
    const hdg = DomUtil.create('td', null, row)
    const dist = DomUtil.create('td', null, row)
    const time = DomUtil.create('td', null, row)
    this.table.insertBefore(row, this.table.children[index + 1])
    this._setValues(current, created, hdg, dist, time)
    this._setPlace(created, place)
    if (next) {
      const nextCells = this.table.children[index + 2].children
      this._setValues(created, next, nextCells[1], nextCells[2], nextCells[3])
    }
  }
  editWaypoint(index, previous, current, next) {
    const currCells = this.table.children[index + 1].children
    this._setValues(previous, current, currCells[1], currCells[2], currCells[3])
    this._setPlace(current, currCells[0])
    if (this.table.children[index + 2]) {
      const nextCells = this.table.children[index + 2].children
      this._setValues(current, next, nextCells[1], nextCells[2], nextCells[3])
    }
  }
  removeWaypoint(index, previous, next) {
    DomUtil.remove(this.table.children[index + 1])
    if (this.table.children[index + 1]) {
      const cells = this.table.children[index + 1].children
      this._setValues(previous, next, cells[1], cells[2], cells[3])
    }
  }
  recalculateTimes(speed) {
    const rows = Array.from(this.table.children).slice(2)
    rows.forEach((row, index) => {
      row.children[3].innerText = Math.floor(parseInt(row.children[2].innerText) / speed * 60) + (index === rows.length - 1 ? ' min' : '')
    })
  }

  _bind() {
    $(() => {
      const speed = document.querySelector('.controls-speed input[type="text"][name="speed"]')
      speed.addEventListener('input', (e) => {
        this.recalculateTimes(e.target.value)
      })
    })
  }
  _setValues(wp1, wp2, hdg, dist, time) {
    if (wp1 && wp2) {
      const speed = this.table.parentElement.parentElement.querySelector('.controls-speed input[type="text"][name="speed"]').value
      const km = Math.floor(wp1.getLatLng().distanceTo(wp2.getLatLng()) / 1000)
      hdg.innerText = getHeading(wp1.getLatLng(), wp2.getLatLng())
      dist.innerText = km
      time.innerText = Math.floor(km / speed * 60)
    } else {
      hdg.innerText = '-'
      dist.innerText = '-'
      time.innerText = '-'
    }
    const rows = Array.from(this.table.children).slice(2)
    rows.pop()
    const summary = this.table.children[this.table.children.length - 1].children
    let totalDist = 0
    let totalTime = 0
    rows.forEach((row) => {
      totalDist += parseInt(row.children[2].innerText)
      totalTime += parseInt(row.children[3].innerText)
    })
    summary[2].innerText = totalDist + ' km'
    summary[3].innerText = totalTime + ' min'
  }
  _setPlace(wpt, place) {
    wpt.fetchAirfield()
      .then(result => {
        if (result.hasOwnProperty('name')) {
          place.innerText = result.name
        } else {
          wpt.fetchPlace()
            .then((results) => {
              const address = results[0].properties.address
              place.innerText = address.village || address.suburb || address.village || address.town || address.city || 'Waypoint'
            })
            .catch(() => {
              place.innerText = 'Waypoint'
            })
        }
      })
  }
}
