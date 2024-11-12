jQuery(document).ready(function ($) {
  if ($("#logout_btn").length > 0) {
    $("#logout_btn").on("click", function (e) {
      e.preventDefault(); // Prevent default action
      var logoutUrl = logoutData.logoutUrl; // Use the PHP-generated logout URL
      window.location.href = logoutUrl; // Redirect to the logout URL
    });
  }
});
