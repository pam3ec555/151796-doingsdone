'use strict';

var expandControls = document.querySelectorAll('.expand-control');
var dir = "/151796-doingsdone/doings-done";

var hidePopups = function() {
  [].forEach.call(document.querySelectorAll('.expand-list'), function(item) {
    item.classList.add('hidden');
  });
};

document.body.addEventListener('click', hidePopups, true);

[].forEach.call(expandControls, function(item) {
  item.addEventListener('click', function() {
    item.nextElementSibling.classList.toggle('hidden');
  });
});

var checkbox = document.getElementsByClassName('checkbox__input')[0];

if (checkbox) {
    checkbox.addEventListener('change', function(event) {
        var is_checked = +event.target.checked;

        window.location = dir + '/index.php?show_completed=' + is_checked;

    });
}

var taskControls = document.querySelector('.radio-button-group');

taskControls.addEventListener('click', function (e) {
    var target = e.target;

    if (target.classList.contains('radio-button__input') && target.nodeName === 'INPUT') {
        var value = target.getAttribute('value');
        window.location = dir + '?task_date=' + value;
    }
});