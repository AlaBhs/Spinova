"use strict";

$(document).ready(function () {
  const formFn = function (target) {
    const form = document.querySelector(`.content-container .${target}`);
    const urlsContainer = document.querySelector(
      `.content-container .${target} .urls-container`
    );
    const regularUrlsContainer = document.querySelector(
      `.content-container .${target} .urls-container .regular-urls`
    );
    const osUrlsContainer = document.querySelector(
      `.content-container .${target} .urls-container .os-specific-urls`
    );
    const autoManCheckBox = document.querySelector(
      `.${target} .auto-man-button-div input[type="checkbox"]`
    );
    const percClickCheckBox = document.querySelector(
      `.${target} .perc-click-button-div input[type="checkbox"]`
    );
    const osFilterCheckBox = document.querySelector(
      `.${target} .os-filter-button-div input[type="checkbox"]`
    );
    $(`#auto-man-${target}-input`).change(function () {
      if ($(`#equal-dist-${target}-input`).is(":checked")) {
        $(this).prop("checked", true);
        $(`.auto-man-span-${target}`).text("Auto");
        return;
      }
    });
    $(`#equal-dist-${target}-input`).change(function () {
      const isEqual = $(this).is(":checked");
      if (isEqual) {
        calcperc();
        autoManCheckBox.checked = true;
        $(`.auto-man-span-${target}`).text("Auto");
      }
      $(".percent-input").each(function () {
        $(this).prop("disabled", isEqual);
      });
    });

    const errorMsg = $(`.${target} p.error-message`);
    const html2 = `
<div class="form-row os-url-pair">
    <div class="form-group col-2 mb-2">
        <input class="form-control destination-num-os" type="text" disabled>
    </div>

    <div class="form-group col-7 mb-2 position-relative">
        <input class="form-control os-destination-input" type="url" name="os_url[]"
            value="https://" placeholder="OS-specific destination..." autocomplete="off" required>

    </div>
    <div class="form-group col-3 mb-2">
        <select class="form-control os-select" name="os[]" required>
            <option value="windows">Windows</option>
            <option value="macos">macOS</option>
            <option value="linux">Linux</option>
            <option value="ios">iOS</option>
            <option value="android">Android</option>
            <option value="other">Other/Default</option>
        </select>
        <button type="button" class="btn btn-danger delete-btn position-absolute">
            <ion-icon name="close-outline"></ion-icon>
        </button>
    </div>
</div>
`;
    const html = `
    <div class="form-row url-pair">
        <div class="form-group col-2 mb-2">
            <input class="form-control destination-num" type="text" disabled value="3">
        </div>
        <div class="form-group col-7 mb-2">
            <input class="form-control destination-input" 
                   type="url" 
                   name="url[]" 
                   value="https://" 
                   placeholder="destination url..." 
                   required>
        </div>
        <div class="form-group col-3 mb-2 position-relative">
            <input class="form-control percent-input" 
                   type="number" 
                   name="perc[]"  
                   autocomplete="off" 
                   placeholder="%" 
                   required>
            <button type="button" class="btn btn-danger delete-btn position-absolute">
              <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>
    </div>
`;
    function addOSUrlField(container) {
      container.append(html2);
    }
    function addUrlField(container) {
      container.append(html);
    }

    // add flields:
    // OS Filter toggle handler
    $(`#os-filter-${target}-input`).change(function () {
      console.log("OS Filter toggled");
      const isOSFilter = $(this).is(":checked");
      // Toggle between regular and OS-specific URLs
      $(".regular-urls").toggle(!isOSFilter);
      $(".os-specific-urls").toggle(isOSFilter);

      if (isOSFilter) {
        $(`#equal-dist-${target}-input, #auto-man-${target}-input, #perc-click-${target}-input`).prop(
          "checked",
          false
        );
        $(".equal-dist-button-div, .auto-man-button-div, .perc-click-button-div").slideUp();
        $(`.perc-click-span-${target}-second`).text("Operating System");
        $(".regular-urls").empty().hide();

        const $osContainer = $(".os-specific-urls").empty().show();
        $(".default-page-div").slideUp();
        // Add initial OS URL fields
        addOSUrlField($osContainer);
        addOSUrlField($osContainer);
        // Wait for DOM update
        setTimeout(() => calcOsDestinationIdx(), 0);
        setTimeout(() => percClickChangeSpan(), 0);
      } else {
        
        $(".equal-dist-button-div, .auto-man-button-div, .perc-click-button-div").slideDown();
        $(`.perc-click-span-${target}-second`).text("Percent (%)");
        // Remove OS URL fields if OS filter is not active
        $(".os-specific-urls").empty().hide();
        const $regularContainer = $(".regular-urls").empty().show();
        // Add initial URL fields
        addUrlField($regularContainer);
        addUrlField($regularContainer);
        // Wait for DOM update
        setTimeout(() => calcDestinationIdx(), 0);
      }

    });
    // Delete field handler (works for both types)
    $(document).on("click", ".delete-btn", function () {
      if ($(this).closest(".url-pair, .os-url-pair").siblings().length > 0) {
        $(this).closest(".url-pair, .os-url-pair").remove();
        calcOsDestinationIdx();
      }
    });

    const addFields = function () {
      // Check which mode is active
      const isOSFilterMode = $(`#os-filter-${target}-input`).is(":checked");
      const container = isOSFilterMode ? osUrlsContainer : regularUrlsContainer;
      const template = isOSFilterMode ? html2 : html;
      const maxFields = 100; // Maximum fields allowed

      if ($(".destination-input, .os-destination-input").length < maxFields) {
        errorMsg.fadeOut();

        // Append the appropriate template
        $(container).append(template);

        // Special handling for each mode
        if (isOSFilterMode) {
          calcOsDestinationIdx();
          if ($(".os-url-pair").length > 1) {
            // Focus the new URL input
            $(".os-url-pair").last().find(".os-destination-input").focus();
          }
        } else {
          // Regular field added
          if (autoManCheckBox.checked === true) {
            calcperc();
          }
          inputFocus();
          calcDestinationIdx();
        }
      } else {
        errorMsg.html(`Can't have more than ${maxFields} fields!`).fadeIn();
      }
    };
    const deleteField = function (targetElement) {
      const destinationFields = $(`.${target} .url-pair`);
      if (destinationFields.length <= 1) {
        errorMsg.html("You must keep at least one destination field!");
        errorMsg.fadeIn();
        return;
      }

      const fieldToRemove = targetElement
        ? $(targetElement).closest(".url-pair")
        : destinationFields.last();
      fieldToRemove.remove();
      if (autoManCheckBox.checked === true) {
        calcperc();
      }
      errorMsg.fadeOut();
      calcDestinationIdx();
      calcOsDestinationIdx();
    };
    const calcDestinationIdx = function () {
      const destIdx = $(`.${target} .destination-num`);

      $(destIdx).each((index, item) => {
        $(item).attr("value", index + 1);
      });
    };
    const calcOsDestinationIdx = function () {
      const destIdx = $(`.${target} .destination-num-os`);

      $(destIdx).each((index, item) => {
        $(item).attr("value", index + 1);
      });
    };
    // initial percentage distribution:
    const calcperc = function () {
      $(`.${target} .percent-input`).val(
        (100 / $(`.${target} .percent-input`).length).toFixed(0)
      );
    };

    const recalcPercentages = function () {
      // Skip if in auto or click mode
      if (!autoManCheckBox.checked || percClickCheckBox.checked) return;

      const $inputs = $(`.${target} .percent-input`);
      const currentVal = parseFloat(this.value) || 0;

      if ($inputs.length > 1) {
        // Validate input (0-100)
        if (currentVal < 0) this.value = 0;
        if (currentVal > 100) this.value = 100;

        const remaining = 100 - currentVal;
        const otherValue = Math.max(
          0,
          (remaining / ($inputs.length - 1)).toFixed(0)
        );

        $inputs.not(this).val(otherValue);
      } else {
        this.value = "100";
      }

      // Optional: trigger change event for other fields
      $inputs.not(this).trigger("change");
    };

    const compareInputVal = function () {
      const inps = $(`.${target} .urls-container input[type='url']`);
      const values = [];

      inps.each(function (index, item) {
        values.push(item.value);
      });

      const sortedValues = values.sort();
      for (let j = 0; j < values.length - 1; j++) {
        if (values[j] == values[j + 1]) {
          errorMsg.html(`Can't have duplicate values!`);
          errorMsg.fadeIn();
          return false;
        }
      }

      // Explicitly return true if validation passes
      return true;
    };

    const comparePercentVal = function () {
      const inps = $(`.${target} input[type='number']`);
      const values = [];

      inps.each(function (index, item) {
        const inputVal = parseFloat(item.value);
        values.push(inputVal);
      });

      const total = values.reduce((acc, currVal) => acc + currVal, 0);
      const containsNegativeVal = values.some((item) => item < 0);

      if (containsNegativeVal) {
        errorMsg.html(
          percClickCheckBox.checked
            ? `Clicks value cannot be negative!`
            : `Percentage value cannot be negative!`
        );
        errorMsg.fadeIn();
        return false;
      }

      if (percClickCheckBox.checked) {
        if (total === 0) {
          errorMsg.html(`Clicks value cannot be zero!`);
          errorMsg.fadeIn();
          return false;
        }
        return true; // For click mode, we don't check total
      } else {
        // Percentage mode validation
        if (total > 100) {
          errorMsg.html(`Percentage value cannot be greater than 100!`);
          errorMsg.fadeIn();
          return false;
        }

        if (total < 100) {
          errorMsg.html(`Percentage value cannot be less than 100!`);
          errorMsg.fadeIn();
          return false;
        }

        return true;
      }
    };

    const autoManChangeSpan = function (e) {
      const span = $(`.auto-man-span-${target}`);

      if (autoManCheckBox.checked === true) {
        $(span).text("Auto");
      } else {
        $(span).text("Manual");
      }
    };

    const percClickChangeSpan = function (e) {
      const span1 = $(`.perc-click-span-${target}`);
      const span2 = $(`.perc-click-span-${target}-second`);
      const inps = $(`.${target} input[type='number']`);
        inps.each(function (index, item) {
          if (percClickCheckBox.checked === true) {
            $(span1).text("Percentage");
            $(span2).text("Clicks");
            $(item).attr({ name: "clicks[]", placeholder: "clicks.." });
            $(".default-page-div").slideDown();
            $(".equal-dist-button-div").slideUp();
            $(`#equal-dist-${target}-input`)
              .prop("checked", false)
              .trigger("change");
            $('.default-page-div input[type="url"]').attr("disabled", false);
          } else {
            if (osFilterCheckBox.checked === false) {
              $(".equal-dist-button-div").slideDown();
            }
            $(span1).text("Clicks");
            $(span2).text("Percent (%)");
            $(item).attr({ name: "perc[]", placeholder: "%" });
            $(".default-page-div").slideUp();
            $('.default-page-div input[type="url"]').attr("disabled", true);
          }
        });
    };

    const resetForm = function (e) {
      const allInputs = $(`.${target} input[type="text"] .name-input, 
                                .${target} input[type="url"], 
                                .${target} input[type="number"]`);

      $(allInputs).each((idx, item) => {
        $(item).val("");
      });

      $(`.${target} input[type="url"]`).val("https://");
      calcperc();
    };

    $(form)
      .on("click", autoManCheckBox, autoManChangeSpan)
      .on("click", percClickCheckBox, percClickChangeSpan)
      .on("click", ".addFieldBtn", addFields)
      .on("click", ".reset-btn", resetForm)
      .on("blur", ".percent-input", recalcPercentages)
      .on("click", ".delete-btn", function () {
        deleteField(this);
      });
    // Add unique os validation before form submission
    $(`form.${target}`).on("submit", function (e) {
      if ($(`#os-filter-${target}-input`).is(":checked")) {
        const osValues = [];
        let hasDuplicates = false;

        $(".os-select").each(function () {
          const os = $(this).val();
          if (osValues.includes(os)) {
            hasDuplicates = true;
            $(this).addClass("is-invalid");
          } else {
            osValues.push(os);
            $(this).removeClass("is-invalid");
          }
        });

        if (hasDuplicates) {
          e.preventDefault();
          $(".error-message")
            .html("Each OS can only be selected once!")
            .fadeIn();
          return false;
        }
      }
      return true;
    });
    const inputFocus = function () {
      $(`.${target} .percent-input`).focus(function () {
        if (percClickCheckBox.checked === true) {
          errorMsg.fadeOut();
          return;
        }
        this.value = "";
        errorMsg.fadeOut();
      });

      $(`.${target} .destination-input`).focus(function () {
        errorMsg.fadeOut();
      });
    };

    // initial distribution:
    inputFocus();

    if (target === "createForm") {
      calcperc();
    }

    if (target === "editForm") {
      calcDestinationIdx();
      calcOsDestinationIdx();
    }
  };

  // copy to clipboard
  const copyLink = function () {
    function copyToClipboard(text) {
      if (window.clipboardData && window.clipboardData.setData) {
        return clipboardData.setData("Text", text);
      } else if (
        document.queryCommandSupported &&
        document.queryCommandSupported("copy")
      ) {
        var textarea = document.createElement("textarea");
        textarea.textContent = text;
        textarea.style.position = "fixed";
        document.body.appendChild(textarea);
        var selection = document.getSelection();
        var range = document.createRange();
        range.selectNode(textarea);
        selection.removeAllRanges();
        selection.addRange(range);
        try {
          return document.execCommand("copy");
        } catch (ex) {
          console.warn("Copy to clipboard failed.", ex);
          return false;
        } finally {
          selection.removeAllRanges();
          document.body.removeChild(textarea);
        }
      }
    }

    $(document).on("click", ".link-slug-copy", function (e) {
      const eElem = $(this);
      const linkID = eElem
        .parent()
        .parent()
        .parent()
        .parent()
        .attr("data-slug");
      const hostName = location.host;
      const text = `${hostName}/${linkID}`;
      const item = $(e.target);

      copyToClipboard(text);
      $(item).attr("title", "Copied!").tooltip("_fixTitle").tooltip("show");
      setTimeout(function () {
        $(item).attr("data-original-title", "Copy");
      }, 1000);
    });
  };

  // error message fade out
  const fadeOutError = function () {
    setTimeout(() => {
      $("header ul li.error-msg").fadeOut();
      $("header ul li.success-msg").fadeOut();
      $("#error-msg").fadeOut();
    }, 6000);
  };

  const darkMode = function () {
    const toggleSwitch = document.querySelector(
      '#dark-mode-button input[type="checkbox"]'
    );

    if (localStorage.theme) {
      if (localStorage.theme === "dark") {
        $("#dark-icon").css("display", "inline-block");
        document.documentElement.setAttribute("data-theme", "dark");
        if (toggleSwitch) {
          toggleSwitch.checked = true;
        }
      } else {
        $("#light-icon").css("display", "inline-block");
        if (toggleSwitch) {
          toggleSwitch.checked = false;
        }
      }
    } else {
      $("#light-icon").css("display", "inline-block");
      if (toggleSwitch) {
        toggleSwitch.checked = false;
      }
    }

    function switchTheme(e) {
      let theme;

      if (e.target.checked) {
        theme = "dark";
        document.documentElement.setAttribute("data-theme", theme);
        $("#dark-icon").css("display", "inline-block");
        $("#light-icon").css("display", "none");
        localStorage.theme = theme;
      } else {
        theme = "light";
        document.documentElement.setAttribute("data-theme", theme);
        $("#dark-icon").css("display", "none");
        $("#light-icon").css("display", "inline-block");
        localStorage.theme = theme;
      }
    }

    if (toggleSwitch) {
      toggleSwitch.addEventListener("change", switchTheme, false);
    }
  };

  // Modal Animation
  const modalAnimation = function () {
    $(".modal").each(function (l) {
      $(this).on("show.bs.modal", function (l) {
        var o = $(this).attr("data-easein");
        "shake" == o
          ? $(".modal-dialog").velocity("callout." + o)
          : "pulse" == o
          ? $(".modal-dialog").velocity("callout." + o)
          : "tada" == o
          ? $(".modal-dialog").velocity("callout." + o)
          : "flash" == o
          ? $(".modal-dialog").velocity("callout." + o)
          : "bounce" == o
          ? $(".modal-dialog").velocity("callout." + o)
          : "swing" == o
          ? $(".modal-dialog").velocity("callout." + o)
          : $(".modal-dialog").velocity("transition." + o);
      });
    });
  };

  // populate buttons with item id
  const populate = function () {
    const deleteBtn = $(
      'main table tbody .rotator-title [data-toggle="modal"]'
    );
    const archiveForm = $("main .modal .modal-footer .archive-form");
    const deleteForm = $("main .modal .modal-footer .delete-form");

    $(deleteBtn).each(function (idx, item) {
      $(item).on("click", function (e) {
        const dataID = $(e.target.parentElement).attr("data-id");

        if ($(deleteForm).hasClass("archive")) {
          deleteForm.attr("action", `/archive/delete/${dataID}?_method=DELETE`);
        } else {
          deleteForm.attr("action", `/delete/${dataID}?_method=DELETE`);
        }

        if (archiveForm) {
          archiveForm.attr("action", `/archive/${dataID}?_method=PUT`);
        }
      });
    });
  };

  const sidebarToggler = function () {
    const body = $("#body");
    const collapseBtn = $("ion-icon[name*='chevron-back-outline']");
    const expandBtn = $("ion-icon[name*='chevron-forward-outline']");

    if ($(window).width() < 576) {
      $("header .header-title").css("display", "none");
      $("header ul li p").css("font-size", "12px");
      $("header ul li").removeClass("mr-3");
      $("header .header-toggle-btn").css("width", "22px");
    }

    /*======== 2. MOBILE OVERLAY ========*/
    if ($(window).width() < 768) {
      $("header .header-toggle-btn").css("display", "block");

      collapseBtn.css({
        transform: "rotate(180deg)",
        transition: " transform 0.01s linear",
      });

      $("#body").removeClass("sidebar-minified");
      $(".header-light .content-wrapper footer .sidebar-toggle").css({
        top: "2%",
        left: "-1%",
      });

      $(".sidebar-toggle").on("click", function () {
        $("body").css("overflow", "hidden");
        $("body").prepend('<div class="mobile-sticky-body-overlay"></div>');
      });

      $(document).on("click", ".mobile-sticky-body-overlay", function (e) {
        $(this).remove();
        $("#body")
          .removeClass("sidebar-mobile-in")
          .addClass("sidebar-mobile-out");
        $("body").css("overflow", "auto");
      });
    }

    /*======== 3. SIDEBAR MENU ========*/
    var sidebar = $(".sidebar");
    if (sidebar.length != 0) {
      $(".sidebar .nav > .has-sub > a").click(function () {
        $(this).parent().siblings().removeClass("expand");
        $(this).parent().toggleClass("expand");
      });

      $(".sidebar .nav > .has-sub .has-sub > a").click(function () {
        $(this).parent().toggleClass("expand");
      });
    }

    /*======== 4. SIDEBAR TOGGLE FOR MOBILE ========*/
    if ($(window).width() < 768) {
      $(document).on("click", ".sidebar-toggle", function (e) {
        e.preventDefault();
        var min = "sidebar-mobile-in",
          min_out = "sidebar-mobile-out",
          body = "#body";
        $(body).hasClass(min)
          ? $(body).removeClass(min).addClass(min_out)
          : $(body).addClass(min).removeClass(min_out);
      });
    }

    /*======== 5. SIDEBAR TOGGLE FOR VARIOUS SIDEBAR LAYOUT ========*/

    if ($(window).width() >= 768) {
      $(".header-light .content-wrapper footer .sidebar-toggle").css({
        position: "relative",
      });

      if (typeof window.isMinified === "undefined") {
        window.isMinified = false;
      }
      if (typeof window.isCollapsed === "undefined") {
        window.isCollapsed = false;
      }

      $("#sidebar-toggler").on("click", function () {
        if (body.hasClass("sidebar-fixed") || body.hasClass("sidebar-static")) {
          $(this)
            .addClass("sidebar-toggle")
            .removeClass("sidebar-offcanvas-toggle");
          if (window.isMinified === false) {
            body
              .removeClass("sidebar-collapse sidebar-minified-out")
              .addClass("sidebar-minified");
            collapseBtn.css({
              transform: "rotate(180deg)",
              transition: " transform 0.3s linear",
            });
            window.isMinified = true;
          } else {
            body.removeClass("sidebar-minified");
            body.addClass("sidebar-minified-out");
            collapseBtn.css({
              transform: "rotate(0deg)",
              transition: " transform 0.3s linear",
            });
            window.isMinified = false;
          }
        }
      });
    }

    if ($(window).width() >= 768 && $(window).width() < 992) {
      if (body.hasClass("sidebar-fixed") || body.hasClass("sidebar-static")) {
        body
          .removeClass("sidebar-collapse sidebar-minified-out")
          .addClass("sidebar-minified");
        window.isMinified = true;
      }
    }
  };

  // session destroy
  const sessionDestroy = function () {
    $(window).on("unload", function (e) {
      $.get("/session/destroy");
    });
  };

  // rotator link
  const hostName = function () {
    const hostNameSpan = $("main table tbody tr .host-name");
    hostNameSpan.text(location.host);
  };

  sidebarToggler();
  hostName();
  copyLink();
  fadeOutError();
  sessionDestroy();
  darkMode();
  modalAnimation();
  populate();
  formFn("createForm");
  formFn("editForm");
});
