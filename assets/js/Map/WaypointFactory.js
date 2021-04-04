import { DivIcon, DomUtil, Popup } from 'leaflet'
import { faMapMarkerAlt, faRoute, faTrash } from '@fortawesome/free-solid-svg-icons'
import Waypoint from './Waypoint'
import { icon } from '@fortawesome/fontawesome-svg-core'

export default class WaypointFactory {
  static createEntryPoint(latlng, onAdd, onDrag, onDragEnd, onAddClick) {
    const removePulse = (object, clickEvent) => {
      DomUtil.removeClass(clickEvent.target._icon, 'map-marker-pulse')
      object.off('click', removePulse)
    }

    const o = new Waypoint(latlng, { icon: this._createWaypointIcon(true), draggable: true })
    const div = DomUtil.create('div', 'center')
    const addBtn = DomUtil.create('button', 'add blue lighten-1 btn')
    addBtn.appendChild(icon(faMapMarkerAlt).node[0])
    addBtn.addEventListener('click', () => onAddClick(o))
    div.appendChild(addBtn)
    const popup = new Popup({ minWidth: 80 }).setContent(div)
    o.bindPopup(popup)
    o.on('add', () => onAdd(o))
    o.on('click', (e) => { removePulse(o, e) })
    o.on('drag', onDrag)
    o.on('dragend', (e) => { removePulse(o, e); onDragEnd(o) })
    return o
  }

  static createTurningPoint(latlng, onAdd, onRemove, onDrag, onDragEnd, onPopupOpen, onAddClick, onRemoveClick, onFinishClick) {
    const o = new Waypoint(latlng, { icon: this._createWaypointIcon(false), draggable: true })
    const div = DomUtil.create('div', 'center')
    const addBtn = DomUtil.create('button', 'add blue lighten-1 btn')
    addBtn.appendChild(icon(faMapMarkerAlt).node[0])
    addBtn.addEventListener('click', () => onAddClick(o))
    const removeBtn = DomUtil.create('button', 'remove blue lighten-1 btn')
    removeBtn.appendChild(icon(faTrash).node[0])
    removeBtn.addEventListener('click', () => onRemoveClick(o))
    const finishBtn = DomUtil.create('button', 'finish blue lighten-1 btn')
    finishBtn.appendChild(icon(faRoute).node[0])
    finishBtn.addEventListener('click', () => onFinishClick(o))
    div.appendChild(addBtn)
    div.appendChild(removeBtn)
    div.appendChild(finishBtn)
    const popup = new Popup({ minWidth: 155 }).setContent(div)
    o.bindPopup(popup)
    o.on('add', () => onAdd(o))
    o.on('drag', onDrag)
    o.on('dragend', () => onDragEnd(o))
    o.on('remove', () => onRemove(o))
    o.on('popupopen', () => onPopupOpen(o))
    return o
  }

  static createStaticWaypoint(latlng) {
    return new Waypoint(latlng, { icon: this._createWaypointIcon(false), draggable: false })
  }

  static _createWaypointIcon(pulse) {
    let classes = 'map-marker map-marker-circle'
    if (pulse) classes = classes + ' map-marker-pulse'
    return new DivIcon({
      className: classes,
      iconSize: null
    })
  }
}
