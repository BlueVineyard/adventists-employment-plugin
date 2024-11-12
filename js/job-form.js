jQuery(document).ready(function ($) {
  $("#submit-job-form").on("submit", function (e) {
    e.preventDefault(); // Prevent the default form submission

    // var formData = $(this).serialize(); // Serialize the form data
    var formData = new FormData(this); // Create a FormData object to handle file upload
    formData.append("security", jobFormAjax.nonce); // Append the nonce to the FormData

    // Append nonce to the form data
    // formData += "&security=" + jobFormAjax.nonce;

    // Perform AJAX request
    $.ajax({
      type: "POST",
      url: jobFormAjax.ajax_url,
      data: formData,
      processData: false, // Important! Prevent jQuery from automatically processing the data
      contentType: false, // Important! Prevent jQuery from setting content-type
      success: function (response) {
        if (response.success) {
          alert(response.data.message); // Show success message
          window.location.href = response.data.redirect_url; // Redirect to the same page
        } else {
          alert(response.data.message); // Show error message
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        alert("An error occurred: " + textStatus);
      },
    });
  });
});

jQuery(document).ready(function ($) {
  // Function to toggle the external application link field
  function toggleExternalApplicationLink() {
    var applyExternallyValue = $(
      'input[name="apply_externally"]:checked'
    ).val();
    console.log(applyExternallyValue); // Log the value to ensure it's correct
    if (applyExternallyValue === "yes") {
      $("#external_application_link_field").css("display", "block"); // Force visibility
    } else {
      $("#external_application_link_field").css("display", "none"); // Force hiding
    }
    console.log("Working");
  }

  // Check on page load if 'Yes' is selected
  toggleExternalApplicationLink();

  // Listen for changes on the "Apply Externally" radio buttons
  $('input[name="apply_externally"]').on("change", function () {
    console.log("Working Change");
    toggleExternalApplicationLink();
  });
});
