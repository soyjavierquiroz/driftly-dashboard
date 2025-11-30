(function ($) {
  $(document).ready(function () {

    var $body = $('body');

    // ============================
    // SIDEBAR MOBILE
    // ============================
    $('.js-d-header-menu-toggle').on('click', function () {
      $body.toggleClass('d-sidebar-open');
    });

    $(document).on('click', function (e) {
      if (
        $body.hasClass('d-sidebar-open') &&
        !$(e.target).closest('.d-sidebar, .js-d-header-menu-toggle').length
      ) {
        $body.removeClass('d-sidebar-open');
      }
    });

    // ========================================
    // CATÁLOGO VDS – Tarjetas + Modal
    // ========================================

    var $modal = $('#d-product-modal');
    var currentProductId = null;

    function openModal() {
      $modal.show();
      $body.addClass('d-modal-open');
    }

    function closeModal() {
      $modal.hide();
      $body.removeClass('d-modal-open');
      currentProductId = null;
    }

    $(document).on('click', '.js-d-modal-close', function () {
      closeModal();
    });

    // ----------------------------------------
    // Cargar detalles en el modal
    // ----------------------------------------
    function loadProductDetails(productId) {
      currentProductId = productId;
      $modal.addClass('d-modal--loading');

      $.post(
        DriftlyDashboard.ajaxUrl,
        {
          action: 'driftly_get_product_details',
          product_id: productId
        },
        function (response) {
          $modal.removeClass('d-modal--loading');

          if (!response || !response.success) {
            alert('No se pudieron cargar los detalles del producto.');
            return;
          }

          var d = response.data;

          $('#dpm-image').attr('src', d.imagen || '').attr('alt', d.nombre);
          $('#dpm-title').text(d.nombre || '');
          $('#dpm-provider').text(d.proveedor_nombre || '');
          $('#dpm-mayorista').text(d.precio_mayorista);
          $('#dpm-sugerido').text(d.precio_sugerido);
          $('#dpm-descripcion-base').html(d.descripcion_base || '');

          $('#dpm-precio-final').val(d.precio_vds);
          $('#dpm-orden').val(d.orden);
          $('#dpm-descripcion-vds').val(d.descripcion_vds || '');

          openModal();
        }
      );
    }

    // ----------------------------------------
    // Click en "Agregar" / "Editar" (tarjeta)
    // ----------------------------------------
    $(document).on('click', '.js-product-open', function () {

      var $btn = $(this);
      var card = $btn.closest('.d-product-card');
      var productId = $btn.data('id');
      var isActive = card.data('activo') == 1;

      if (!isActive) {
        // Activar primero y luego abrir modal
        $btn.prop('disabled', true);

        $.post(
          DriftlyDashboard.ajaxUrl,
          {
            action: 'driftly_toggle_product_vds',
            producto_id: productId
          },
          function (response) {
            $btn.prop('disabled', false);

            if (!response || !response.success) {
              alert('No se pudo activar el producto.');
              return;
            }

            card.data('activo', 1);
            card.find('.d-badge').removeClass('d-badge--danger').addClass('d-badge--success').text('En mi tienda');
            $btn.text('Editar');

            // Añadir botón "Desactivar" si no existe
            if (!card.find('.js-product-toggle').length) {
              $('<button type="button" class="d-btn d-btn--ghost js-product-toggle" data-id="' + productId + '">Desactivar</button>')
                .appendTo(card.find('.d-product-card__actions'));
            }

            loadProductDetails(productId);
          }
        );
      } else {
        // Ya activo → solo abrir modal
        loadProductDetails(productId);
      }
    });

    // ----------------------------------------
    // Click en "Desactivar" (tarjeta)
    // ----------------------------------------
    $(document).on('click', '.js-product-toggle', function () {

      var $btn = $(this);
      var productId = $btn.data('id');
      var card = $btn.closest('.d-product-card');

      $btn.prop('disabled', true);

      $.post(
        DriftlyDashboard.ajaxUrl,
        {
          action: 'driftly_toggle_product_vds',
          producto_id: productId
        },
        function (response) {
          $btn.prop('disabled', false);

          if (!response || !response.success) {
            alert('No se pudo cambiar el estado.');
            return;
          }

          var activo = response.data.activo;

          if (activo == 1) {
            card.data('activo', 1);
            card.find('.d-badge').removeClass('d-badge--danger').addClass('d-badge--success').text('En mi tienda');
            card.find('.js-product-open').text('Editar');
          } else {
            card.data('activo', 0);
            card.find('.d-badge').removeClass('d-badge--success').addClass('d-badge--danger').text('No agregado');
            card.find('.js-product-open').text('Agregar');
            card.find('.js-product-toggle').remove();
            card.find('.js-my-price').remove();
          }
        }
      );
    });

    // ----------------------------------------
    // Guardar cambios desde el modal
    // ----------------------------------------
    $('#dpm-save').on('click', function () {

      if (!currentProductId) return;

      var precio = $('#dpm-precio-final').val();
      var orden = $('#dpm-orden').val();
      var descripcion = $('#dpm-descripcion-vds').val();

      $.post(
        DriftlyDashboard.ajaxUrl,
        {
          action: 'driftly_update_product_vds',
          product_id: currentProductId,
          precio: precio,
          orden: orden,
          descripcion: descripcion
        },
        function (response) {
          if (!response || !response.success) {
            alert('No se pudieron guardar los cambios.');
            return;
          }

          // Actualizar "Mi precio" en la tarjeta
          var card = $('.d-product-card[data-product-id="' + currentProductId + '"]');
          var myPriceEl = card.find('.js-my-price');

          if (!myPriceEl.length) {
            var pricesBlock = card.find('.d-product-card__prices');
            myPriceEl = $('<div class="d-product-card__price-row"><span class="label">Mi precio</span><span class="value js-my-price"></span></div>');
            pricesBlock.append(myPriceEl);
          }

          myPriceEl.text(precio);

          closeModal();
        }
      );
    });

  });
})(jQuery);
