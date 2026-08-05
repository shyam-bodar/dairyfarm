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

  document.querySelectorAll('.confirm-action').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(el.getAttribute('data-confirm') || 'Are you sure?')) {
        e.preventDefault();
      }
    });
  });
});


document.querySelectorAll('.qty-box').forEach(function (box) {

    const input = box.querySelector("input");
    const minus = box.querySelector(".qty-minus");
    const plus = box.querySelector(".qty-plus");

    if (!input) return;

    const max = parseInt(input.getAttribute("max")) || 9999;

    if (minus) {

        minus.addEventListener("click", function () {

            let value = parseInt(input.value) || 1;

            if (value > 1) {
                input.value = value - 1;
            }

        });

    }

    if (plus) {

        plus.addEventListener("click", function () {

            let value = parseInt(input.value) || 1;

            if (value < max) {
                input.value = value + 1;
            }

        });

    }

});