import { LatLngBounds, Polyline } from 'leaflet'
import TrackTable from './TrackTable'
import WaypointFactory from './WaypointFactory'

export default class StaticTrack {
  constructor(map, latlngs) {
    this.map = map
    this.table = new TrackTable()
    if (latlngs) {
      const wpts = []
      latlngs.forEach(latlng => {
        const wpt = this.addWaypoint(latlng)
        wpts.push(wpt)
        const index = wpts.length - 1
        this.table.addWaypoint(index, wpts[index - 1], wpt)
      })
      this._createConnection(wpts).addTo(this.map)
      map.fitBounds(new LatLngBounds(latlngs))
    }
  }
  addWaypoint(latlng) {
    const wp = WaypointFactory.createStaticWaypoint(latlng)
    wp.addTo(this.map)
    return wp
  }
  _createConnection(wpts) {
    return new Polyline(wpts.map(wp => wp.getLatLng()), { color: '#FF00FF', weight: 4 })
  }
}
