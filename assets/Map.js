import './css/map.scss'
import $ from './js/DOMLoaded'
import { map } from './js/Map/Main'

$(() => {
  map.requireMap()
})
