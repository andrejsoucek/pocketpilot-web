import EditableTrack from './EditableTrack'
import { GeoJSON } from 'leaflet'
import Main from '../Main'
import MapFactory from './MapFactory'
import StaticTrack from './StaticTrack'

function start(win) {
  const main = new Main(win)

  return {
    requireMap: () => {
      const selector = '#map'

      const initMap = (editable) => {
        const map = MapFactory.createMap()
        const trackData = map.getContainer().dataset.track
        if (trackData) {
          const geoJSON = new GeoJSON(JSON.parse(trackData))
          if (editable) {
            new EditableTrack(map, GeoJSON.coordsToLatLngs(geoJSON.getLayers()[0].feature.geometry.coordinates))
          } else {
            new StaticTrack(map, GeoJSON.coordsToLatLngs(geoJSON.getLayers()[0].feature.geometry.coordinates))
          }
        } else {
          if (editable) {
            const track = new EditableTrack(map)
            const addFirstWpt = (e) => {
              track.addWaypoint(e.latlng)
              map.off('click')
            }
            map.on('click', addFirstWpt)
          }
        }
      }

      const apply = (el) => {
        const editable = el.dataset.editable
        switch (editable) {
          case 'true':
            initMap(true)
            break
          case 'false':
            initMap(false)
            break
          default:
            throw Error('Invalid [data-editable] value.')
        }
      }
      main.attach(selector, apply)
    }
  }
}

export const map = start(window)
