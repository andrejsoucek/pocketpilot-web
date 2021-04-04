import AirspaceChart from './AirspaceChart'
import AirspaceTable from './AirspaceTable'
import naja from 'naja'

export default class Airspace {
  constructor(track) {
    this.track = track
    this.table = new AirspaceTable()
    this.chart = new AirspaceChart()
  }
  invalidate() {
    this.table.loading(true)
    this.chart.loading(true)
    const airspace = naja.makeRequest(
      'GET',
      '/api/v1/airspace',
      {
        path: this._formatCoordinates(this.track.getWaypoints())
      },
      {
        history: false
      }
    )
    const terrain = naja.makeRequest(
      'GET',
      '/api/v1/terrain',
      {
        path: this._formatCoordinates(this.track.getWaypoints())
      },
      {
        history: false
      }
    )
    Promise.all([airspace, terrain])
      .then((data) => {
        const merged = { airspace: data[0], terrain: data[1] }
        this.table.update(merged)
        this.chart.update(merged)
      })
      .catch(e => {
        console.log(e)
      })
      .finally(() => {
        this.table.loading(false)
        this.chart.loading(false)
      })
  }

  _formatCoordinates(wpts) {
    const coordinates = []
    wpts.forEach((wpt) => {
      coordinates.push(`${wpt.getLatLng().lat},${wpt.getLatLng().lng}`)
    })
    if (coordinates.length < 2) {
      coordinates.push(coordinates[0])
    }
    return coordinates.join('|')
  }
}
