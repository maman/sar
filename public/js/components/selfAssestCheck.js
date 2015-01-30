'use strict';

if ($('[data-check]').length) {
  var ratio = parseFloat($('[data-ratio]').val());
  var text = document.querySelector('[data-check-percentage]');
  var textJq = $('[data-check-percentage]');
  $('[data-check]').on('click', function() {
    var percentage = parseFloat(text.value);
    if (isNaN(percentage)) {
      percentage = 0;
    }
    if ($(this).is(':checked')) {
      text.value = percentage + ratio;
      textJq.prop('max', percentage + ratio);
    } else {
      text.value = percentage - ratio;
      textJq.prop('max', percentage - ratio);
    }
  });
};
