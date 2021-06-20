function toggleMainNav() {
  var elem = document.getElementById('mainnav');
  if (elem.classList.contains('hidden')) {
    elem.classList.remove('hidden');
  } else {
    elem.classList.add('hidden');
  }
}
