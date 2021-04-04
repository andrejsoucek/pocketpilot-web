import { LatLng } from 'leaflet'

export function getHeading(latlngA, latlngB) {
  const d2r = Math.PI / 180
  const r2d = 180 / Math.PI
  const lat1 = latlngA.lat * d2r
  const lat2 = latlngB.lat * d2r
  const dLon = (latlngB.lng - latlngA.lng) * d2r
  const y = Math.sin(dLon) * Math.cos(lat2)
  const x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(lat2) * Math.cos(dLon)
  let hdg = Math.atan2(y, x)
  hdg = parseInt(hdg * r2d)
  hdg = (hdg + 360) % 360
  if (hdg === 0) {
    return 360
  }
  return hdg
}

/**
 * From leaflet geometryutil.
 * @author
 * @param latlng
 * @param heading
 * @param distance
 * @returns LatLng
 */
export function calculateDestination(latlng, heading, distance) {
  heading = (heading + 360) % 360
  const rad = Math.PI / 180
  const radInv = 180 / Math.PI
  const R = 6378137 // approximation of Earth's radius
  const lon1 = latlng.lng * rad
  const lat1 = latlng.lat * rad
  const rheading = heading * rad
  const sinLat1 = Math.sin(lat1)
  const cosLat1 = Math.cos(lat1)
  const cosDistR = Math.cos(distance / R)
  const sinDistR = Math.sin(distance / R)
  const lat2 = Math.asin(sinLat1 * cosDistR + cosLat1 * sinDistR * Math.cos(rheading))
  let lon2 = lon1 + Math.atan2(Math.sin(rheading) * sinDistR * cosLat1, cosDistR - sinLat1 * Math.sin(lat2))
  lon2 = lon2 * radInv
  lon2 = lon2 > 180 ? lon2 - 360 : lon2 < -180 ? lon2 + 360 : lon2
  return new LatLng(lat2 * radInv, lon2)
}

export function middle(latlngA, latlngB) {
  const d2r = Math.PI / 180
  const r2d = 180 / Math.PI
  const dLon = d2r * (latlngB.lng - latlngA.lng)
  // convert to radians
  const lat1 = latlngA.lat * d2r
  const lat2 = latlngB.lat * d2r
  const lon1 = latlngA.lng * d2r
  const Bx = Math.cos(lat2) * Math.cos(dLon)
  const By = Math.cos(lat2) * Math.sin(dLon)
  const lat3 = Math.atan2(Math.sin(lat1) + Math.sin(lat2), Math.sqrt((Math.cos(lat1) + Bx) * (Math.cos(lat1) + Bx) + By * By))
  const lon3 = lon1 + Math.atan2(By, Math.cos(lat1) + Bx)
  return new LatLng(lat3 * r2d, lon3 * r2d)
}
