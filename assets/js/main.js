document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('.menu-toggle');
  var nav = document.querySelector('.primary-nav');
  if (!toggle || !nav) return;

  toggle.addEventListener('click', function () {
    var isOpen = nav.classList.toggle('mobile-open');
    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  });

  // Close the menu if someone taps outside it
  document.addEventListener('click', function (e) {
    if (!nav.classList.contains('mobile-open')) return;
    if (nav.contains(e.target) || toggle.contains(e.target)) return;
    nav.classList.remove('mobile-open');
    toggle.setAttribute('aria-expanded', 'false');
  });

  var searchToggle = document.querySelector('.search-toggle');
  var searchPanel = document.getElementById('header-search');
  if (!searchToggle || !searchPanel) return;

  searchToggle.addEventListener('click', function () {
    var isOpen = searchPanel.classList.toggle('open');
    searchToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    if (isOpen) {
      var input = searchPanel.querySelector('input[type="search"]');
      if (input) input.focus();
    }
  });

  document.addEventListener('click', function (e) {
    if (!searchPanel.classList.contains('open')) return;
    if (searchPanel.contains(e.target) || searchToggle.contains(e.target)) return;
    searchPanel.classList.remove('open');
    searchToggle.setAttribute('aria-expanded', 'false');
  });
});
