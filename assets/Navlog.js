import './css/navlog.scss'
import $ from './js/DOMLoaded'
import { navlog } from './js/Navlog/Main'

$(() => {
  navlog.requireTable()
})
