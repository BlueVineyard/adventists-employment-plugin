jQuery(document).ready(function ($) {
  if ($("#blog-filter").length > 0) {
    // On click of category buttons
    $(".blog-category-button").on("click", function () {
      // Remove active class from all buttons
      $(".blog-category-button").removeClass("active");
      // Add active class to the clicked button
      $(this).addClass("active");

      let selectedCategory = $(this).data("category");

      // If "All" is selected, set selectedCategory to an empty array
      if (selectedCategory === "all") {
        selectedCategory = [];
      }

      loadPosts(selectedCategory, 1); // Load posts for the selected category, first page
    });

    // Handle pagination clicks
    $("#blog-filter-results").on("click", ".pagination a", function (e) {
      e.preventDefault();
      let selectedCategory = $(".blog-category-button.active").data("category");

      // If "All" is selected, set selectedCategory to an empty array
      if (selectedCategory === "all") {
        selectedCategory = [];
      }

      let page = $(this).data("page"); // Get the page number from the pagination link

      loadPosts(selectedCategory, page); // Load posts for the selected category, selected page
    });
  }

  // Function to load posts based on category and page
  function loadPosts(category, page) {
    $.ajax({
      url: blogFilterParams.ajax_url,
      type: "POST",
      data: {
        action: "filter_blogs",
        categories: category,
        page: page,
      },
      success: function (response) {
        $("#blog-filter-results").html(response);
      },
    });
  }
});
