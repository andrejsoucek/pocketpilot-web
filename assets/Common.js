import './css/main.scss'
import $ from './js/DOMLoaded'
import { common } from './js/Common/Main'

$(() => {
  common.requireIcons()
  common.requireNetteForms()
  common.requireNaja()
  common.requireSideNav()
  common.requireModal()
  common.requirePulse()
  common.requireTooltip()
  common.requireFlashMessage()
  common.requireConfirmation()
  common.requireTabs()
})
