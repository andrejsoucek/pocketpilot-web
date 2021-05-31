import { DomUtil, Map, TileLayer } from 'leaflet'

const MAP_BASE = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
const MAP_OFM = 'https://nwy-tiles-api.prod.newaydata.com/tiles/{z}/{x}/{y}.png?path=2105/aero/latest'

export default class MapFactory {
  static createMap() {
    const map = new Map(
      'map',
      { minZoom: 8, maxZoom: 12 }
    )
    const osmLink = '<a href="http://openstreetmap.org" target="_blank">OpenStreetMap</a>'
    const ground = new TileLayer(
      MAP_BASE, {
        attribution: '&copy; ' + osmLink + ' Contributors',
        maxZoom: 12
      })
    const ofmLink = '<a href="https://www.openflightmaps.org/" target="_blank">OpenFlightMaps</a>'
    const ofm = new TileLayer(MAP_OFM, {
      attribution: ofmLink,
      maxZoom: 12
    })
    map.setView([50.075, 14.437], 8)
    map.zoomControl.setPosition('topright')
    ground.addTo(map)
    ofm.addTo(map)

    let zoom = map.getZoom()
    DomUtil.addClass(map.getContainer(), 'zoom-' + zoom)
    map.on('zoomend', () => {
      DomUtil.removeClass(map.getContainer(), 'zoom-' + zoom)
      zoom = map.getZoom()
      DomUtil.addClass(map.getContainer(), 'zoom-' + zoom)
    })
    return map
  }
}

