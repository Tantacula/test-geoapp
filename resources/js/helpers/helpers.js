export const supportsLocalStorage = function () {
  return !!localStorage
}

export const hasProperties = function (obj, props = []) {
  return props.every(prop => Object.prototype.hasOwnProperty.call(obj, prop))
}