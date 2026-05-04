(function ($) {
  'use strict';

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function setMessage($root, message, type) {
    $root.find('[data-rio-message]')
      .removeClass('is-error is-success')
      .addClass(type ? 'is-' + type : '')
      .text(message || '');
  }

  function getDirectionsUrl(store) {
    if (store.map_url) {
      return store.map_url;
    }

    if (store.lat && store.lng) {
      return 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(store.lat + ',' + store.lng);
    }

    if (store.address) {
      return 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(store.address);
    }

    return '';
  }

  function openResultsPopup($root) {
    $root.find('[data-rio-results-popup]')
      .addClass('is-open')
      .attr('aria-hidden', 'false');
  }

  function closeResultsPopup($root) {
    $root.find('[data-rio-results-popup]')
      .removeClass('is-open')
      .attr('aria-hidden', 'true');
  }

  function renderResults($root, postalCode, payload) {
    var results = payload.results || [];
    var saveEnabled = !!payload.save_enabled;

    if (!results.length) {
      $root.find('[data-rio-results]').html('');
      closeResultsPopup($root);
      setMessage($root, 'No address found.', 'error');
      return;
    }

    var html = results.map(function (row, index) {
      var address = row.address || {};
      var store = row.nearest_store || {};
      var directionsUrl = getDirectionsUrl(store);
      var distanceLabel = store.distance_type === 'driving' ? 'Driving distance' : 'Distance';
      var durationText = store.duration_text ? ' (' + store.duration_text + ')' : '';
      var actions = '';

      if (directionsUrl) {
        actions += '<a class="rio-onemap-result__directions" href="' + escapeHtml(directionsUrl) + '" target="_blank" rel="noopener noreferrer">Get directions</a>';
      }

      if (saveEnabled) {
        actions += '<button type="button" class="rio-onemap-result__choose" data-rio-choose-address>Choose this address</button>';
      }

      return '' +
        '<article class="rio-onemap-result ' + (index === 0 ? 'is-primary' : '') + '" data-result-index="' + index + '">' +
          '<div class="rio-onemap-result__store">' +
            '<h4>' + escapeHtml(store.name) + '</h4>' +
            '<p class="rio-onemap-result__line rio-onemap-result__line--search">Search address: ' + escapeHtml(address.address) + '</p>' +
            '<p class="rio-onemap-result__line rio-onemap-result__line--address">Store address: ' + escapeHtml(store.address) + '</p>' +
            (store.phone ? '<p class="rio-onemap-result__line rio-onemap-result__line--phone">Phone Number:' + escapeHtml(store.phone) + '</p>' : '') +
            '<p class="rio-onemap-result__line rio-onemap-result__line--distance">' + escapeHtml(distanceLabel) + ': ' + escapeHtml(store.distance_km) + ' km' + escapeHtml(durationText) + '</p>' +
          '</div>' +
          (actions ? '<div class="rio-onemap-result__actions">' + actions + '</div>' : '') +
        '</article>';
    }).join('');

    $root.data('rio-results-data', { postalCode: postalCode, results: results });
    $root.find('[data-rio-results]').html(html);
    openResultsPopup($root);
    setMessage($root, 'Found nearest store.', 'success');
  }

  $(document).on('click', '[data-rio-search-btn]', function () {
    var $root = $(this).closest('[data-rio-onemap-search]');
    var postalCode = $.trim($root.find('[data-rio-postal-code]').val());

    if (!/^\d{6}$/.test(postalCode)) {
      setMessage($root, 'Please enter a valid 6-digit Singapore postal code.', 'error');
      return;
    }

    closeResultsPopup($root);
    setMessage($root, 'Searching...', '');
    $root.find('[data-rio-results]').html('');

    $.post(RioOneMapSearch.ajaxUrl, {
      action: 'rio_onemap_search',
      nonce: RioOneMapSearch.nonce,
      postal_code: postalCode
    }).done(function (response) {
      if (!response || !response.success) {
        setMessage($root, response && response.data ? response.data.message : 'Search failed.', 'error');
        return;
      }
      renderResults($root, postalCode, response.data);
    }).fail(function () {
      setMessage($root, 'Search request failed.', 'error');
    });
  });

  $(document).on('click', '[data-rio-choose-address]', function () {
    var $root = $(this).closest('[data-rio-onemap-search]');
    var index = parseInt($(this).closest('[data-result-index]').attr('data-result-index'), 10);
    var data = $root.data('rio-results-data');
    var row = data && data.results ? data.results[index] : null;

    if (!row) return;

    var address = row.address || {};
    var store = row.nearest_store || {};

    setMessage($root, 'Saving address...', '');

    $.post(RioOneMapSearch.ajaxUrl, {
      action: 'rio_onemap_save_address',
      nonce: RioOneMapSearch.nonce,
      postal_code: data.postalCode,
      selected_address: address.address,
      selected_lat: address.lat,
      selected_lng: address.lng,
      nearest_store_id: store.id,
      nearest_store_name: store.name,
      distance_km: store.distance_km
    }).done(function (response) {
      if (!response || !response.success) {
        setMessage($root, response && response.data ? response.data.message : 'Save failed.', 'error');
        return;
      }
      setMessage($root, 'Address selected and saved.', 'success');
    }).fail(function () {
      setMessage($root, 'Save request failed.', 'error');
    });
  });

  $(document).on('click', '[data-rio-close-results]', function () {
    closeResultsPopup($(this).closest('[data-rio-onemap-search]'));
  });

  $(document).on('click', '[data-rio-results-popup]', function (event) {
    if (event.target === this) {
      closeResultsPopup($(this).closest('[data-rio-onemap-search]'));
    }
  });

  $(document).on('keyup', function (event) {
    if (event.key === 'Escape') {
      $('[data-rio-results-popup].is-open').each(function () {
        closeResultsPopup($(this).closest('[data-rio-onemap-search]'));
      });
    }
  });
})(jQuery);
