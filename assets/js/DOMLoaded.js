export default function(cb) {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', cb)
  } else {
    cb.call()
  }
}
