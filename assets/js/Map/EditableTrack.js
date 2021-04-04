import { DomUtil, LatLngBounds, Polyline } from 'leaflet'
import { calculateDestination, getHeading, middle } from './Utils'
import Airspace from './Airspace/Airspace'
import Controls from './Controls'
import TrackTable from './TrackTable'
import WaypointFactory from './WaypointFactory'

export default class EditableTrack {
  constructor(map, latlngs) {
    this.map = map
    this.waypoints = []
    this.line = null
    this.trackTable = new TrackTable()
    this.airspace = new Airspace(this)
    this.controls = new Controls(this)
    if (latlngs) {
      latlngs.forEach((latlng, i) => {
        const wp = this.addWaypoint(latlng, this.waypoints.length, i === latlngs.length - 1)
        DomUtil.removeClass(wp._icon, 'map-marker-pulse')
      })
      map.fitBounds(new LatLngBounds(latlngs))
    }
  }
  addWaypoint(latlng, index, invalidateAirspace = true) {
    const wp = this._createWaypoint(latlng, index, invalidateAirspace)
    wp.addTo(this.map)
    return wp
  }
  removeWaypoint(wp) {
    wp.removeFrom(this.map)
  }
  getWaypoints() {
    return this.waypoints
  }

  _createConnection() {
    const connection = new Polyline(this.waypoints.map(wp => wp.getLatLng()), { color: '#FF00FF', weight: 4 })
    this.line = connection
    return connection
  }

  _createWaypoint(latlng, index, invalidateAirspaceOnAdd) {
    let wp
    if (this.waypoints.length < 1) {
      wp = WaypointFactory.createEntryPoint(
        latlng,
        (o) => this._onWaypointAdd(o, invalidateAirspaceOnAdd),
        (o) => this._onWaypointDrag(o),
        (o) => this._onWaypointDragEnd(o),
        (o) => this._onAddClick(o)
      )
    } else {
      wp = WaypointFactory.createTurningPoint(
        latlng,
        (o) => this._onWaypointAdd(o, invalidateAirspaceOnAdd),
        (o) => this._onWaypointRemove(o),
        (o) => this._onWaypointDrag(o),
        (o) => this._onWaypointDragEnd(o),
        (o) => this._onPopupOpen(o),
        (o) => this._onAddClick(o),
        (o) => this._onDeleteClick(o),
        (o) => this._onFinishClick(o)
      )
    }
    this.waypoints.splice(index, 0, wp)
    return wp
  }

  _onAddClick(wp) {
    const index = this.waypoints.indexOf(wp)
    const newWp = this.addWaypoint(this._predictPositionAfter(wp), index + 1)
    newWp.openPopup()
  }

  _onDeleteClick(wp) {
    this.removeWaypoint(wp)
  }

  _onFinishClick(wp) {
    this.addWaypoint(this.waypoints[0].getLatLng(), this.waypoints.length)
    wp.closePopup()
  }

  _onPopupOpen(wp) {
    if (this.waypoints.length > 2 && this.waypoints.indexOf(wp) === this.waypoints.length - 1) {
      DomUtil.addClass(wp.getPopup().getContent(), 'last-wpt')
    } else {
      DomUtil.removeClass(wp.getPopup().getContent(), 'last-wpt')
    }
  }

  _onWaypointAdd(wp, invalidateAirspace) {
    if (this.waypoints.length > 1) {
      this._recreateConnection()
    }
    const index = this.waypoints.indexOf(wp)
    if (this.waypoints[index]) {
      this.trackTable.addWaypoint(index, this.waypoints[index - 1], wp, this.waypoints[index + 1])
    } else {
      this.trackTable.addWaypoint(index, this.waypoints[index - 1], wp)
    }
    if (invalidateAirspace) {
      this.airspace.invalidate()
    }
  }

  _onWaypointDrag() {
    this._recreateConnection()
  }

  _onWaypointDragEnd(wp) {
    const index = this.waypoints.indexOf(wp)
    this.trackTable.editWaypoint(index, this.waypoints[index - 1], wp, this.waypoints[index + 1])
    wp.openPopup()
    this.airspace.invalidate()
  }

  _onWaypointRemove(wp) {
    const index = this.waypoints.indexOf(wp)
    this.waypoints.splice(index, 1)
    this._recreateConnection()
    this.trackTable.removeWaypoint(index, this.waypoints[index - 1], this.waypoints[index])
    this.airspace.invalidate()
  }

  _predictPositionAfter(wp) {
    const index = this.waypoints.indexOf(wp)
    const current = this.waypoints[index]
    const previous = this.waypoints[index > 0 ? index - 1 : index]
    if (this.waypoints.length > index + 1) {
      return middle(current.getLatLng(), this.waypoints[index + 1].getLatLng())
    } else {
      const heading = getHeading(previous.getLatLng(), current.getLatLng())
      return calculateDestination(current.getLatLng(), heading, 5000)
    }
  }

  _recreateConnection() {
    if (this.line) {
      this.line.removeFrom(this.map)
    }
    const conn = this._createConnection()
    conn.addTo(this.map)
  }
}
