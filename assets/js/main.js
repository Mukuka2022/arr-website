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

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-longread-slider]').forEach(function (slider) {
    var track = slider.querySelector('.longread-track');
    var slides = Array.prototype.slice.call(track.children);
    if (slides.length < 2) {
      var prev = slider.querySelector('.slider-arrow.prev');
      var next = slider.querySelector('.slider-arrow.next');
      if (prev) prev.style.display = 'none';
      if (next) next.style.display = 'none';
      return;
    }

    var dotsWrap = slider.querySelector('.slider-dots');
    var dots = slides.map(function (_, i) {
      var dot = document.createElement('button');
      dot.type = 'button';
      dot.setAttribute('aria-label', 'Go to article ' + (i + 1));
      dot.addEventListener('click', function () { go(i); reset(); });
      dotsWrap.appendChild(dot);
      return dot;
    });

    var index = 0;
    var timer;

    function go(i) {
      index = (i + slides.length) % slides.length;
      track.style.transform = 'translateX(-' + (index * 100) + '%)';
      dots.forEach(function (d, di) { d.classList.toggle('active', di === index); });
    }

    function reset() {
      clearInterval(timer);
      timer = setInterval(function () { go(index + 1); }, 5000);
    }

    slider.querySelector('.slider-arrow.prev').addEventListener('click', function () { go(index - 1); reset(); });
    slider.querySelector('.slider-arrow.next').addEventListener('click', function () { go(index + 1); reset(); });
    slider.addEventListener('mouseenter', function () { clearInterval(timer); });
    slider.addEventListener('mouseleave', reset);

    go(0);
    reset();
  });
});
