export default class AirspaceChartSettings {
  static get CANVAS_PADDING_TOP() {
    return 50
  }
  static get CANVAS_PADDING_BOTTOM() {
    return 20
  }
  static get CANVAS_PADDING_LEFT() {
    return 50
  }
  static get CANVAS_PADDING_RIGHT() {
    return 50
  }
  static get CANVAS_FONT_SIZE() {
    return 12
  }
  static get CANVAS_FONT() {
    return AirspaceChartSettings.CANVAS_FONT_SIZE + 'px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif'
  }
  static get Y_AXIS_LABELS() {
    return ['0', '1000', '2000', '3000', '4000', '5000', '6000']
  }
  static getColorFor(airspaceType) {
    switch (airspaceType) {
      case 'ATZ':
        return 'rgba(171, 183, 183, 0.5)'
      case 'CTR':
        return 'rgba(25, 181, 254, 0.5)'
      case 'D':
        return 'rgba(248, 148, 6, 0.5)'
      case 'P':
        return 'rgba(207, 0, 15, 0.5)'
      case 'R':
        return 'rgba(242, 38, 19, 0.5)'
      case 'TMA':
        return 'rgba(89, 171, 227, 0.5)'
      case 'TRA_GA':
        return 'rgba(0, 230, 64, 0.5)'
      case 'TRA':
      case 'TSA':
        return 'rgba(255, 210, 125, 0.5)'
      default:
        return 'rgba(171, 183, 183, 0.2)'
    }
  }
}
