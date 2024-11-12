/**
 * Add Resume Submission Script
 */
jQuery(document).ready(function ($) {
  // Handle form submission
  $("#submit-resume-form").on("submit", function (e) {
    e.preventDefault(); // Prevent default form submission

    // Create a new FormData object to handle file uploads and form data
    var formData = new FormData(this);

    // Append the nonce to the form data
    formData.append("security", resumeFormAjax.nonce);

    // Perform the AJAX request
    $.ajax({
      url: resumeFormAjax.ajax_url, // AJAX handler URL
      type: "POST",
      data: formData,
      contentType: false, // Required for file upload
      processData: false, // Prevent jQuery from processing the data
      success: function (response) {
        if (response.success) {
          // Show success message
          alert(response.data.message);

          // Optionally redirect to a new URL after form submission
          window.location.href = response.data.redirect_url;
        } else {
          // Show error message
          alert(response.data.message);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // Display generic error message if the request fails
        alert("An error occurred: " + textStatus);
      },
    });
  });
});

/**
 * Add Resume Navigation Script
 */
jQuery(document).ready(function ($) {
  if ($("#add_resume").length > 0) {
    var $nav = $(".add_resume-nav");
    var $form = $("#submit-resume-form");
    var $container = $("#add_resume");
    var navTop = $nav.offset().top; // Initial top position of the nav
    var containerTop = $container.offset().top; // Top of the container
    var containerWidth = $container.width(); // Container width for right alignment

    // Function to update form bottom position
    function updateFormBottom() {
      return $form.offset().top + $form.outerHeight();
    }

    // Update container width on window resize
    $(window).resize(function () {
      containerWidth = $container.width();
    });

    $(window).scroll(function () {
      var scrollTop = $(window).scrollTop();
      var navHeight = $nav.outerHeight();
      var formBottom = updateFormBottom(); // Recalculate form bottom
      var containerBottom = $container.offset().top + $container.outerHeight();

      // Check if the user has scrolled past the nav
      if (scrollTop > navTop) {
        // Check if the nav is exceeding the form's bottom
        if (scrollTop + navHeight < containerBottom) {
          $nav.css({
            position: "fixed",
            top: "0",
            right:
              $(window).width() -
              $container.offset().left -
              containerWidth +
              "px",
            width: "100%",
          });
        } else {
          // Stop the nav at the form's bottom
          $nav.css({
            position: "absolute",
            top: formBottom - navHeight - containerTop + "px",
            right: "0",
            width: "100%",
          });
        }
      } else {
        // Restore original position when scrolled above the nav
        $nav.css({
          position: "relative",
          top: "0",
          right: "0",
        });
      }
    });
  }

  if ($("#add_resume").length > 0) {
    // Smooth scroll for the navigation links and adding active class
    $(".add_resume-nav-link").click(function (e) {
      e.preventDefault(); // Prevent default anchor behavior

      // Get the ID of the section from the clicked link
      var targetId = $(this)
        .attr("class")
        .split(" ")[1]
        .replace("add_resume-", "");

      // Scroll to the respective section with smooth animation
      $("html, body").animate(
        {
          scrollTop: $("#" + targetId).offset().top - 20, // Offset to adjust spacing from the top
        },
        500
      ); // Animation duration (500ms)

      // Remove 'active' class from all navigation links
      $(".add_resume-nav-link").removeClass("active");

      // Add 'active' class to the clicked link
      $(this).addClass("active");
    });
  }
});

/**
 * Candidate Experience Script with 'Present' Option
 */
jQuery(document).ready(function ($) {
  function initializeDaterangepicker() {
    $('input[name^="candidate_experience"][name$="[date]"]').daterangepicker({
      locale: {
        format: "MMMM, YYYY", // Customize the date format
      },
      autoUpdateInput: false, // Prevent automatic date display
    });

    // Update the input field when a date range is selected
    $('input[name^="candidate_experience"][name$="[date]"]').on(
      "apply.daterangepicker",
      function (ev, picker) {
        $(this).val(
          picker.startDate.format("MMMM, YYYY") +
            " to " +
            picker.endDate.format("MMMM, YYYY")
        );
      }
    );

    // Clear the input field if the user cancels the selection
    $('input[name^="candidate_experience"][name$="[date]"]').on(
      "cancel.daterangepicker",
      function (ev, picker) {
        $(this).val("");
      }
    );

    // Handle 'Present' checkbox
    $(document).on("change", ".current-job", function () {
      var $dateInput = $(this)
        .closest(".career-history-entry")
        .find('input[name^="candidate_experience"][name$="[date]"]');
      if ($(this).is(":checked")) {
        // Replace the end date with "Present"
        var dateRange = $dateInput.val().split(" to ");
        if (dateRange.length > 1) {
          $dateInput.val(dateRange[0] + " to Present");
        } else {
          $dateInput.val("Present");
        }
      } else {
        // If unchecked, reset the end date to a valid range
        var startDate = moment().startOf("month");
        var endDate = moment().endOf("month");
        $dateInput.val(
          startDate.format("MMMM, YYYY") + " to " + endDate.format("MMMM, YYYY")
        );
      }
    });
  }

  // Call the function to initialize Daterangepicker on page load
  initializeDaterangepicker();

  // Handle adding new experience dynamically
  $("#add-experience").on("click", function (e) {
    e.preventDefault();

    var newEntry = $("#career-history-template")
      .clone()
      .removeAttr("id")
      .show();

    var entryCount = $("#career-history-repeater .career-history-entry").length;

    newEntry.find("input, textarea").each(function () {
      var currentName = $(this).attr("name");
      var currentId = $(this).attr("id");

      if (currentName) {
        var newName = currentName.replace(/\[\d+\]/, "[" + entryCount + "]");
        $(this).attr("name", newName);
      }
      if (currentId) {
        var newId = currentId.replace(/_\d+$/, "_" + entryCount);
        $(this).attr("id", newId);
      }

      $(this).val("");
    });

    $("#career-history-repeater").append(newEntry);

    // Re-initialize Daterangepicker for the newly added date field
    newEntry
      .find('input[name^="candidate_experience"][name$="[date]"]')
      .daterangepicker({
        locale: {
          format: "MMMM, YYYY", // Customize the date format
        },
        autoUpdateInput: false, // Prevent automatic date display
      });

    // Handle 'Present' checkbox for newly added entries
    $(document).on("change", ".current-job", function () {
      var $dateInput = $(this)
        .closest(".career-history-entry")
        .find('input[name^="candidate_experience"][name$="[date]"]');
      if ($(this).is(":checked")) {
        var dateRange = $dateInput.val().split(" to ");
        if (dateRange.length > 1) {
          $dateInput.val(dateRange[0] + " to Present");
        } else {
          $dateInput.val("Present");
        }
      } else {
        var startDate = moment().startOf("month");
        var endDate = moment().endOf("month");
        $dateInput.val(
          startDate.format("MMMM, YYYY") + " to " + endDate.format("MMMM, YYYY")
        );
      }
    });
  });

  // Handle removing experience dynamically
  $(document).on("click", ".remove-experience", function (e) {
    e.preventDefault();

    if ($("#career-history-repeater .career-history-entry").length > 1) {
      $(this).closest(".career-history-entry").remove();
    } else {
      $(this).closest(".career-history-entry").find("input, textarea").val("");
    }
  });
});

/**
 * Candidate Education Script
 */
jQuery(document).ready(function ($) {
  // Initialize Daterangepicker for all fields with a specific name attribute
  function initializeDaterangepicker() {
    $('input[name^="candidate_education"][name$="[date]"]').daterangepicker({
      locale: {
        format: "MMMM, YYYY", // Customize the date format
      },
      autoUpdateInput: false, // Prevent automatic date display
    });

    // Update the input field when a date range is selected
    $('input[name^="candidate_education"][name$="[date]"]').on(
      "apply.daterangepicker",
      function (ev, picker) {
        $(this).val(
          picker.startDate.format("MMMM, YYYY") +
            " to " +
            picker.endDate.format("MMMM, YYYY")
        );
      }
    );

    // Clear the input field if the user cancels the selection
    $('input[name^="candidate_education"][name$="[date]"]').on(
      "cancel.daterangepicker",
      function (ev, picker) {
        $(this).val("");
      }
    );
  }

  // Call the function to initialize Daterangepicker on page load
  initializeDaterangepicker();

  // Handle adding new education dynamically
  $("#add-education").on("click", function (e) {
    e.preventDefault();

    // Clone the hidden education-history-template
    var newEntry = $("#education-history-template")
      .clone()
      .removeAttr("id")
      .show();

    // Get the current number of entries
    var entryCount = $(
      "#education-history-repeater .education-history-entry"
    ).length;

    // Update the cloned entry with unique IDs and Names
    newEntry.find("input, textarea").each(function () {
      var currentName = $(this).attr("name");
      var currentId = $(this).attr("id");

      // Replace the index in 'name' and 'id' attributes with the new entryCount
      if (currentName) {
        var newName = currentName.replace(/\[\d+\]/, "[" + entryCount + "]");
        $(this).attr("name", newName);
      }
      if (currentId) {
        var newId = currentId.replace(/_\d+$/, "_" + entryCount);
        $(this).attr("id", newId);
      }

      // Clear the values in the cloned entry
      $(this).val("");
    });

    // Append the new entry
    $("#education-history-repeater").append(newEntry);

    // Re-initialize Daterangepicker for the newly added date field
    newEntry
      .find('input[name^="candidate_education"][name$="[date]"]')
      .daterangepicker({
        locale: {
          format: "MMMM, YYYY", // Customize the date format
        },
        autoUpdateInput: false, // Prevent automatic date display
      });

    // Update the input field when a date range is selected
    newEntry
      .find('input[name^="candidate_education"][name$="[date]"]')
      .on("apply.daterangepicker", function (ev, picker) {
        $(this).val(
          picker.startDate.format("MMMM, YYYY") +
            " to " +
            picker.endDate.format("MMMM, YYYY")
        );
      });

    // Clear the input field if the user cancels the selection
    newEntry
      .find('input[name^="candidate_education"][name$="[date]"]')
      .on("cancel.daterangepicker", function (ev, picker) {
        $(this).val("");
      });
  });

  // Handle removing education dynamically
  $(document).on("click", ".remove-education", function (e) {
    e.preventDefault();

    // If more than one entry exists, remove the clicked one
    if ($("#education-history-repeater .education-history-entry").length > 1) {
      $(this).closest(".education-history-entry").remove();
    } else {
      // If only one entry remains, clear its values instead of removing
      $(this)
        .closest(".education-history-entry")
        .find("input, textarea")
        .val("");
    }
  });
});

/**
 * Candidate Certification Script
 */
jQuery(document).ready(function ($) {
  // Handle adding new education dynamically
  $("#add-certification").on("click", function (e) {
    e.preventDefault();

    // Clone the hidden education-history-template
    var newEntry = $("#certifications-history-template")
      .clone()
      .removeAttr("id")
      .show();

    // Get the current number of entries
    var entryCount = $(
      "#certifications-history-repeater .certifications-history-entry"
    ).length;

    // Update the cloned entry with unique IDs and Names
    newEntry.find("input").each(function () {
      var currentName = $(this).attr("name");
      var currentId = $(this).attr("id");

      // Replace the index in 'name' and 'id' attributes with the new entryCount
      if (currentName) {
        var newName = currentName.replace(/\[\d+\]/, "[" + entryCount + "]");
        $(this).attr("name", newName);
      }
      if (currentId) {
        var newId = currentId.replace(/_\d+$/, "_" + entryCount);
        $(this).attr("id", newId);
      }

      // Clear the values in the cloned entry
      $(this).val("");
    });

    // Append the new entry
    $("#certifications-history-repeater").append(newEntry);
  });

  // Handle removing certification dynamically
  $(document).on("click", ".remove-certification", function (e) {
    e.preventDefault();

    // If more than one entry exists, remove the clicked one
    if (
      $("#certifications-history-repeater .certifications-history-entry")
        .length > 1
    ) {
      $(this).closest(".certifications-history-entry").remove();
    } else {
      // If only one entry remains, clear its values instead of removing
      $(this).closest(".certifications-history-entry").find("input").val("");
    }
  });
});

/**
 * Candidate SKills Script
 */
jQuery(document).ready(function ($) {
  let selectedSkills = [];

  // Autocomplete for the skills input field
  $("#resume_skills_input").autocomplete({
    source: function (request, response) {
      $.ajax({
        url: resumeFormAjax.ajax_url,
        type: "GET",
        data: {
          action: "fetch_skills",
          security: resumeFormAjax.nonce,
          term: request.term,
        },
        success: function (data) {
          response(data);
        },
      });
    },
    select: function (event, ui) {
      addSkill(ui.item.value);
      $(this).val("");
      return false;
    },
  });

  // Handle adding a new skill
  $("#resume_skills_input").keypress(function (e) {
    if (e.which === 13) {
      e.preventDefault();
      let skill = $(this).val();
      if (skill && !selectedSkills.includes(skill)) {
        addSkill(skill);
        $(this).val("");
      }
    }
  });

  // Add skill to the list
  function addSkill(skill) {
    if (!selectedSkills.includes(skill)) {
      selectedSkills.push(skill);
      $("#skills-container").append(
        '<span class="skill-tag" data-skill="' +
          skill +
          '">' +
          skill +
          '<a href="#" class="remove-skill">x</a></span>'
      );
      updateSkillsInput();
    }
  }

  // Remove skill from the list
  $(document).on("click", ".remove-skill", function (e) {
    e.preventDefault();
    let skillToRemove = $(this).parent().data("skill");
    selectedSkills = selectedSkills.filter((s) => s !== skillToRemove);
    $(this).parent().remove();
    updateSkillsInput();
  });

  // Update the hidden input with selected skills
  function updateSkillsInput() {
    // Always make sure the selectedSkills array is reflected in the hidden input field
    const skillsValue = selectedSkills.join(",");
    $("#resume_skills").val(skillsValue);

    // Log to console for debugging purposes (optional)
    console.log("Updated skills:", skillsValue);
  }

  // Initialize the skills on page load if there are pre-existing values
  const existingSkills = $("#resume_skills").val();
  if (existingSkills) {
    selectedSkills = existingSkills.split(",");
    selectedSkills.forEach((skill) => {
      addSkill(skill); // Display the preloaded skills
    });
  }
});
