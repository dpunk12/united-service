/**
 * One Ten United – Main JavaScript
 * Version: 1.0.0
 */

( function () {
  'use strict';

  /* =====================================================================
     DOM READY
     ===================================================================== */
  document.addEventListener( 'DOMContentLoaded', function () {
    initMobileNav();
    initStickyHeader();
    initSmoothScroll();
    initBackToTop();
    initLazyImages();
    initFormValidation();
    initWooCartSpinner();
    initNavDropdownKeyboard();
    initSearchForm();
  } );

  /* =====================================================================
     MOBILE HAMBURGER NAV TOGGLE
     ===================================================================== */
  function initMobileNav() {
    var toggle   = document.getElementById( 'nav-toggle' );
    var nav      = document.getElementById( 'primary-nav' );
    var body     = document.body;
    var OPEN_CLS = 'nav-open';

    if ( ! toggle || ! nav ) {
      return;
    }

    function openNav() {
      nav.classList.add( OPEN_CLS );
      toggle.setAttribute( 'aria-expanded', 'true' );
      body.style.overflow = 'hidden';
    }

    function closeNav() {
      nav.classList.remove( OPEN_CLS );
      toggle.setAttribute( 'aria-expanded', 'false' );
      body.style.overflow = '';
    }

    toggle.addEventListener( 'click', function () {
      var isOpen = toggle.getAttribute( 'aria-expanded' ) === 'true';
      isOpen ? closeNav() : openNav();
    } );

    // Close when a menu link is clicked (navigating away).
    var navLinks = nav.querySelectorAll( 'a' );
    navLinks.forEach( function ( link ) {
      link.addEventListener( 'click', function () {
        closeNav();
      } );
    } );

    // Close on Escape key.
    document.addEventListener( 'keydown', function ( e ) {
      if ( e.key === 'Escape' && nav.classList.contains( OPEN_CLS ) ) {
        closeNav();
        toggle.focus();
      }
    } );

    // Close when clicking outside nav.
    document.addEventListener( 'click', function ( e ) {
      if (
        nav.classList.contains( OPEN_CLS ) &&
        ! nav.contains( e.target ) &&
        e.target !== toggle &&
        ! toggle.contains( e.target )
      ) {
        closeNav();
      }
    } );
  }

  /* =====================================================================
     STICKY HEADER ON SCROLL
     ===================================================================== */
  function initStickyHeader() {
    var header    = document.getElementById( 'site-header' );
    var topBar    = document.querySelector( '.top-bar' );
    var SCROLL_CLS = 'scrolled';
    var threshold  = 60;

    if ( ! header ) {
      return;
    }

    function handleScroll() {
      if ( window.scrollY > threshold ) {
        header.classList.add( SCROLL_CLS );
        // Shrink top bar on scroll for extra height on mobile.
        if ( topBar ) {
          topBar.style.display = window.innerWidth < 768 ? 'none' : '';
        }
      } else {
        header.classList.remove( SCROLL_CLS );
        if ( topBar ) {
          topBar.style.display = '';
        }
      }
    }

    window.addEventListener( 'scroll', handleScroll, { passive: true } );
    handleScroll(); // Run once on load.
  }

  /* =====================================================================
     SMOOTH SCROLL FOR ANCHOR LINKS
     ===================================================================== */
  function initSmoothScroll() {
    var OFFSET = 80; // Account for fixed header height.

    document.querySelectorAll( 'a[href^="#"]' ).forEach( function ( anchor ) {
      anchor.addEventListener( 'click', function ( e ) {
        var href = anchor.getAttribute( 'href' );

        // Ignore empty or just "#" anchors.
        if ( ! href || href === '#' || href.length <= 1 ) {
          return;
        }

        var target = document.querySelector( href );
        if ( ! target ) {
          return;
        }

        e.preventDefault();

        var targetTop = target.getBoundingClientRect().top + window.pageYOffset - OFFSET;

        window.scrollTo( {
          top:      targetTop,
          behavior: 'smooth',
        } );

        // Update URL hash without jumping.
        if ( history.pushState ) {
          history.pushState( null, null, href );
        }
      } );
    } );
  }

  /* =====================================================================
     BACK TO TOP BUTTON
     ===================================================================== */
  function initBackToTop() {
    var btn        = document.getElementById( 'back-to-top' );
    var SHOW_AFTER = 400;
    var VISIBLE_CLS = 'visible';

    if ( ! btn ) {
      return;
    }

    function toggleVisibility() {
      if ( window.scrollY > SHOW_AFTER ) {
        btn.classList.add( VISIBLE_CLS );
      } else {
        btn.classList.remove( VISIBLE_CLS );
      }
    }

    window.addEventListener( 'scroll', toggleVisibility, { passive: true } );
    toggleVisibility();

    btn.addEventListener( 'click', function ( e ) {
      e.preventDefault();
      window.scrollTo( { top: 0, behavior: 'smooth' } );
    } );
  }

  /* =====================================================================
     LAZY LOADING IMAGES VIA INTERSECTIONOBSERVER
     ===================================================================== */
  function initLazyImages() {
    // Native lazy loading is set via the loading="lazy" attribute in PHP.
    // This provides a polyfill for older browsers.
    if ( 'loading' in HTMLImageElement.prototype ) {
      return; // Browser supports native lazy loading.
    }

    if ( ! ( 'IntersectionObserver' in window ) ) {
      return; // No support – images load normally.
    }

    var lazyImages = document.querySelectorAll( 'img[loading="lazy"]' );

    if ( ! lazyImages.length ) {
      return;
    }

    var observer = new IntersectionObserver(
      function ( entries ) {
        entries.forEach( function ( entry ) {
          if ( entry.isIntersecting ) {
            var img = entry.target;
            if ( img.dataset.src ) {
              img.src = img.dataset.src;
            }
            if ( img.dataset.srcset ) {
              img.srcset = img.dataset.srcset;
            }
            img.classList.add( 'lazy-loaded' );
            observer.unobserve( img );
          }
        } );
      },
      {
        rootMargin: '200px 0px',
        threshold:  0.01,
      }
    );

    lazyImages.forEach( function ( img ) {
      observer.observe( img );
    } );
  }

  /* =====================================================================
     FORM FIELD VALIDATION HELPERS
     ===================================================================== */
  function initFormValidation() {
    var forms = document.querySelectorAll( 'form.booking-form, form.wpcf7-form, form.otu-form' );

    forms.forEach( function ( form ) {
      var requiredFields = form.querySelectorAll( '[required]' );

      requiredFields.forEach( function ( field ) {
        field.addEventListener( 'blur', function () {
          validateField( field );
        } );

        field.addEventListener( 'input', function () {
          if ( field.classList.contains( 'field-error' ) ) {
            validateField( field );
          }
        } );
      } );

      form.addEventListener( 'submit', function ( e ) {
        var isValid = true;
        requiredFields.forEach( function ( field ) {
          if ( ! validateField( field ) ) {
            isValid = false;
          }
        } );
        if ( ! isValid ) {
          e.preventDefault();
          // Focus the first invalid field.
          var firstInvalid = form.querySelector( '.field-error' );
          if ( firstInvalid ) {
            firstInvalid.focus();
          }
        }
      } );
    } );
  }

  /**
   * Validate a single field. Returns true if valid, false otherwise.
   *
   * @param {HTMLElement} field
   * @returns {boolean}
   */
  function validateField( field ) {
    removeFieldError( field );
    var value = field.value.trim();

    if ( field.type === 'checkbox' ) {
      if ( ! field.checked ) {
        showFieldError( field, 'This field is required.' );
        return false;
      }
      return true;
    }

    if ( ! value ) {
      showFieldError( field, 'This field is required.' );
      return false;
    }

    if ( field.type === 'email' && ! isValidEmail( value ) ) {
      showFieldError( field, 'Please enter a valid email address.' );
      return false;
    }

    if ( field.type === 'tel' && ! isValidPhone( value ) ) {
      showFieldError( field, 'Please enter a valid phone number.' );
      return false;
    }

    return true;
  }

  function showFieldError( field, message ) {
    field.classList.add( 'field-error' );
    field.setAttribute( 'aria-invalid', 'true' );

    var errorEl = document.createElement( 'span' );
    errorEl.className = 'error-msg';
    errorEl.setAttribute( 'role', 'alert' );
    errorEl.textContent = message;

    var parent = field.closest( '.form-group' ) || field.parentNode;
    parent.appendChild( errorEl );
  }

  function removeFieldError( field ) {
    field.classList.remove( 'field-error' );
    field.removeAttribute( 'aria-invalid' );

    var parent = field.closest( '.form-group' ) || field.parentNode;
    var errorEl = parent.querySelector( '.error-msg' );
    if ( errorEl ) {
      errorEl.remove();
    }
  }

  function isValidEmail( email ) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( email );
  }

  function isValidPhone( phone ) {
    return /^[\d\s\-()+]{7,15}$/.test( phone );
  }

  /* =====================================================================
     WOOCOMMERCE ADD-TO-CART AJAX SPINNER
     ===================================================================== */
  function initWooCartSpinner() {
    document.addEventListener( 'click', function ( e ) {
      var btn = e.target.closest( '.ajax_add_to_cart' );
      if ( ! btn ) {
        return;
      }

      btn.classList.add( 'loading' );
      btn.setAttribute( 'disabled', 'disabled' );
      btn.setAttribute( 'aria-busy', 'true' );

      // Insert spinner SVG if not present.
      if ( ! btn.querySelector( '.cart-spinner' ) ) {
        var spinner = document.createElement( 'span' );
        spinner.className   = 'cart-spinner';
        spinner.setAttribute( 'aria-hidden', 'true' );
        spinner.innerHTML =
          '<svg class="spin" viewBox="0 0 24 24" width="16" height="16">' +
          '<circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/>' +
          '</svg>';
        btn.prepend( spinner );
      }
    } );

    // Listen for WooCommerce 'added_to_cart' event and re-enable buttons.
    document.body.addEventListener( 'added_to_cart', function () {
      document.querySelectorAll( '.ajax_add_to_cart.loading' ).forEach( function ( btn ) {
        btn.classList.remove( 'loading' );
        btn.removeAttribute( 'disabled' );
        btn.removeAttribute( 'aria-busy' );
        var spinner = btn.querySelector( '.cart-spinner' );
        if ( spinner ) {
          spinner.remove();
        }
      } );
    } );
  }

  /* =====================================================================
     KEYBOARD NAVIGATION FOR NAV DROPDOWNS
     ===================================================================== */
  function initNavDropdownKeyboard() {
    var menuItems = document.querySelectorAll( '.primary-menu li' );

    menuItems.forEach( function ( item ) {
      var subMenu = item.querySelector( '.sub-menu' );
      if ( ! subMenu ) {
        return;
      }

      // Open on Enter/Space when parent link is focused.
      var parentLink = item.querySelector( 'a' );
      if ( parentLink ) {
        parentLink.addEventListener( 'keydown', function ( e ) {
          if ( ( e.key === 'Enter' || e.key === ' ' ) && subMenu ) {
            if ( subMenu.style.display === 'block' ) {
              subMenu.style.display = '';
            } else {
              subMenu.style.display = 'block';
            }
          }
          if ( e.key === 'ArrowDown' ) {
            e.preventDefault();
            var firstLink = subMenu.querySelector( 'a' );
            if ( firstLink ) {
              firstLink.focus();
            }
          }
        } );
      }

      // Close sub-menu when tabbing out of last item.
      var subLinks = subMenu.querySelectorAll( 'a' );
      if ( subLinks.length ) {
        subLinks[ subLinks.length - 1 ].addEventListener( 'keydown', function ( e ) {
          if ( e.key === 'Tab' && ! e.shiftKey ) {
            subMenu.style.display = '';
          }
          if ( e.key === 'Escape' ) {
            subMenu.style.display = '';
            if ( parentLink ) {
              parentLink.focus();
            }
          }
        } );
      }
    } );
  }

  /* =====================================================================
     SEARCH FORM – LIVE SUGGESTIONS PLACEHOLDER
     (Add your own AJAX endpoint to extend this)
     ===================================================================== */
  function initSearchForm() {
    var searchInputs = document.querySelectorAll( '.search-form__input' );

    searchInputs.forEach( function ( input ) {
      // Clear button.
      var clearBtn = document.createElement( 'button' );
      clearBtn.type = 'button';
      clearBtn.className = 'search-clear-btn';
      clearBtn.setAttribute( 'aria-label', 'Clear search' );
      clearBtn.innerHTML =
        '<svg viewBox="0 0 24 24" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
      clearBtn.style.cssText =
        'display:none;position:absolute;right:80px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#888;padding:4px;';

      var wrapper = input.parentElement;
      wrapper.style.position = 'relative';
      wrapper.insertBefore( clearBtn, wrapper.querySelector( '.search-form__submit' ) );

      input.addEventListener( 'input', function () {
        clearBtn.style.display = input.value ? 'block' : 'none';
      } );

      clearBtn.addEventListener( 'click', function () {
        input.value = '';
        clearBtn.style.display = 'none';
        input.focus();
      } );
    } );
  }

  /* =====================================================================
     CSS ANIMATION HELPER: Add .spin style dynamically
     ===================================================================== */
  ( function addSpinStyle() {
    var style = document.createElement( 'style' );
    style.textContent =
      '@keyframes spin{to{transform:rotate(360deg)}}' +
      '.spin{animation:spin .8s linear infinite;transform-origin:center;}';
    document.head.appendChild( style );
  } )();

} )();
