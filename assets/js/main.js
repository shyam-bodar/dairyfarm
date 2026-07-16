document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('.menu-toggle');
  var nav = document.querySelector('.nav-links');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('open');
    });
  }

  var sideToggle = document.querySelector('.side-toggle');
  var sidebar = document.querySelector('.admin-sidebar');
  if (sideToggle && sidebar) {
    sideToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
  }

  document.querySelectorAll('.qty-box').forEach(function (box) {
    var input = box.querySelector('input');
    var minus = box.querySelector('.qty-minus');
    var plus = box.querySelector('.qty-plus');
    if (!input) return;
    if (minus) minus.addEventListener('click', function () {
      input.value = Math.max(1, parseInt(input.value || 1) - 1);
      input.form && input.form.requestSubmit ? input.form.requestSubmit() : input.form.submit();
    });
    if (plus) plus.addEventListener('click', function () {
      input.value = parseInt(input.value || 1) + 1;
      input.form && input.form.requestSubmit ? input.form.requestSubmit() : input.form.submit();
    });
  });

  document.querySelectorAll('.confirm-action').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(el.getAttribute('data-confirm') || 'Are you sure?')) {
        e.preventDefault();
      }
    });
  });
});
