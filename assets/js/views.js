(function () {
  if (!window.arrViews || !arrViews.endpoint) return;

  var sent = false;

  function report() {
    if (sent) return;
    sent = true;
    // keepalive so the request still goes out if the reader navigates away
    // the moment the timer fires.
    fetch(arrViews.endpoint, { method: 'POST', keepalive: true }).catch(function () {});
  }

  // A few seconds on the page before counting, so an instant bounce or a
  // link prefetch isn't recorded as somebody reading the article.
  function start() {
    setTimeout(report, (arrViews.delay || 3) * 1000);
  }

  // Pages opened in a background tab shouldn't start the clock until the
  // reader actually looks at them.
  if (document.visibilityState === 'hidden') {
    document.addEventListener('visibilitychange', function onShow() {
      if (document.visibilityState !== 'visible') return;
      document.removeEventListener('visibilitychange', onShow);
      start();
    });
  } else {
    start();
  }
})();
