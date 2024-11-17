jQuery(document).ready(function ($) {
  // Form submission logic
  $("#submit-job-form").on("submit", function (e) {
    e.preventDefault(); // Prevent the default form submission

    var formData = new FormData(this); // Create a FormData object to handle file upload
    formData.append("security", jobFormAjax.nonce); // Append the nonce to the FormData

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

  // Toggle the external application link field
  function toggleExternalApplicationLink() {
    var applyExternallyValue = $(
      'input[name="apply_externally"]:checked'
    ).val();
    if (applyExternallyValue === "yes") {
      $("#external_application_link_field").css("display", "block"); // Show the field
    } else {
      $("#external_application_link_field").css("display", "none"); // Hide the field
    }
  }

  // Check on page load if 'Yes' is selected
  toggleExternalApplicationLink();

  // Listen for changes on the "Apply Externally" radio buttons
  $('input[name="apply_externally"]').on("change", function () {
    toggleExternalApplicationLink();
  });

  // Dynamically update company details based on selected employer
  $("#employer").on("change", function () {
    var employerID = $(this).val();
    if (!employerID) {
      // Reset the fields if no employer is selected
      $("#company_logo_preview").html("");
      $("#company_name").val("");
      $("#company_website").val("");
      return;
    }

    // Perform AJAX request to fetch employer details
    $.ajax({
      url: jobFormAjax.ajax_url,
      type: "POST",
      data: {
        action: "fetch_employer_details",
        employer_id: employerID,
        security: jobFormAjax.nonce,
      },
      success: function (response) {
        if (response.success) {
          const { company_logo, company_name, company_website } = response.data;

          // Update the fields with employer details
          if (company_logo) {
            $("#company_logo_preview").html(
              '<img src="' + company_logo + '" alt="Company Logo" />'
            );
          } else {
            $("#company_logo_preview").html("");
          }

          $("#company_name").val(company_name || "");
          $("#company_website").val(company_website || "");
        } else {
          alert(response.data.message); // Show error message if any
        }
      },
      error: function () {
        alert("An error occurred while fetching employer details.");
      },
    });
  });
});
