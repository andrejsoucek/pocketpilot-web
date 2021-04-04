import { DomUtil } from 'leaflet'

export default class AirspaceTable {
  constructor() {
    this.table = this.renderEmpty()
  }
  renderEmpty() {
    const wrapper = document.getElementById('airspace').querySelector('.info-table-wrapper')
    const table = DomUtil.create('table', 'info-table airspace')
    wrapper.insertBefore(table, wrapper.children[0])
    const header = DomUtil.create('tr', 'info-table-header', table)
    DomUtil.create('th', '', header).innerText = 'TYPE'
    DomUtil.create('th', '', header).innerText = 'NAME'
    DomUtil.create('th', '', header).innerText = 'LOWER'
    DomUtil.create('th', '', header).innerText = 'UPPER'
    return table
  }
  update(data) {
    this.table.parentElement.removeChild(this.table)
    this.table = this.renderEmpty()
    data.airspace.forEach((as) => {
      const row = DomUtil.create('tr', null)
      const type = DomUtil.create('td', null, row)
      const name = DomUtil.create('td', null, row)
      const from = DomUtil.create('td', null, row)
      const to = DomUtil.create('td', null, row)
      type.innerText = as.type
      name.innerText = as.name
      from.innerText = `${as.verticalBounds.lowerDatum} ${as.verticalBounds.lower}`
      to.innerText = `${as.verticalBounds.upperDatum} ${as.verticalBounds.upper}`
      this.table.appendChild(row)
    })
  }
  loading(loading) {
    const loader = this.table.parentElement.parentElement.querySelector('.progress')
    if (loading) {
      loader.style.display = 'table'
    } else {
      loader.style.display = 'none'
    }
  }
}
