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
  var copyBtn = document.querySelector('.share-copy');
  if (!copyBtn) return;

  var timer;

  function confirmCopied() {
    copyBtn.classList.add('copied');
    clearTimeout(timer);
    timer = setTimeout(function () { copyBtn.classList.remove('copied'); }, 2000);
  }

  copyBtn.addEventListener('click', function () {
    var url = copyBtn.getAttribute('data-share-url');
    if (!url) return;

    // navigator.clipboard needs a secure context — it is undefined on plain
    // http, which is exactly how this site is served in local development.
    // Fall back to a throwaway textarea so the button still works there.
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(url).then(confirmCopied, legacyCopy);
    } else {
      legacyCopy();
    }

    function legacyCopy() {
      var field = document.createElement('textarea');
      field.value = url;
      field.setAttribute('readonly', '');
      field.style.position = 'fixed';
      field.style.opacity = '0';
      document.body.appendChild(field);
      field.select();
      try { document.execCommand('copy'); confirmCopied(); } catch (e) {}
      document.body.removeChild(field);
    }
  });
});

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-slider]').forEach(function (slider) {
    var track = slider.querySelector('.slider-track');
    var dotsWrap = slider.querySelector('.slider-dots');
    if (!track) return;
    var slides = Array.prototype.slice.call(track.children);
    if (slides.length < 2) {
      if (dotsWrap) dotsWrap.style.display = 'none';
      return;
    }

    var count = slides.length;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Advancing past the last slide by wrapping the index back to 0 animates
    // the track backwards through every slide, which reads as a rewind — very
    // obvious on a two-slide track, where it just ping-pongs. Instead, append a
    // copy of the first slide so "next" from the last slide keeps moving
    // forward into an identical-looking frame, then snap silently back to the
    // real first slide once the transition ends.
    //
    // Skipped entirely under prefers-reduced-motion: transitions are disabled
    // there (see the global rule in prototype.css), so transitionend would
    // never fire and the track would stick on the clone. With no animation
    // there is no rewind to hide anyway.
    if (!reduceMotion) {
      var clone = slides[0].cloneNode(true);
      clone.setAttribute('aria-hidden', 'true');
      clone.querySelectorAll('a, button, input').forEach(function (el) {
        el.setAttribute('tabindex', '-1');
      });
      track.appendChild(clone);
    }

    var dots = slides.map(function (_, i) {
      var dot = document.createElement('button');
      dot.type = 'button';
      dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
      dot.addEventListener('click', function () { go(i); reset(); });
      dotsWrap.appendChild(dot);
      return dot;
    });

    var index = 0;
    var timer;

    function paint(i, instant) {
      track.style.transition = instant ? 'none' : '';
      track.style.transform = 'translateX(-' + (i * 100) + '%)';
      dots.forEach(function (d, di) { d.classList.toggle('active', di === i % count); });
    }

    function go(i) {
      index = reduceMotion ? (i + count) % count : i;
      paint(index);
    }

    function next() {
      // index === count lands on the clone; transitionend resets it to 0.
      go(reduceMotion ? index + 1 : (index >= count ? 1 : index + 1));
    }

    track.addEventListener('transitionend', function (e) {
      if (e.target !== track || e.propertyName !== 'transform') return;
      if (index !== count) return;
      index = 0;
      paint(0, true);
      // Force a reflow so the re-enabled transition applies to the *next*
      // move rather than being coalesced into this instant jump.
      void track.offsetWidth;
      track.style.transition = '';
    });

    function reset() {
      clearInterval(timer);
      timer = setInterval(next, 5000);
    }

    slider.addEventListener('mouseenter', function () { clearInterval(timer); });
    slider.addEventListener('mouseleave', reset);

    go(0);
    reset();
  });
});
