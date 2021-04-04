import 'leaflet-control-geocoder'
import { CRS, Marker } from 'leaflet'
import naja from 'naja'

export default class Waypoint extends Marker {
  constructor(latlng, options) {
    super(latlng, options)
    this.geocoder = L.Control.Geocoder.nominatim()
  }
  fetchAirfield() {
    return naja.makeRequest('GET', '/api/v1/poi', { lat: this.getLatLng().lat, lng: this.getLatLng().lng }, { history: false })
  }
  fetchPlace() {
    return new Promise((resolve) => {
      this.geocoder.reverse(this.getLatLng(), CRS.scale(14), resolve)
    })
  }
}
