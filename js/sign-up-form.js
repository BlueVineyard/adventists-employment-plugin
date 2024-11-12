jQuery(document).ready(function ($) {
  $("#candidate-div").click(function () {
    $("#candidate-form").show();
    $("#employer-form").hide();
  });

  $("#employer-div").click(function () {
    $("#employer-form").show();
    $("#candidate-form").hide();
  });
});
