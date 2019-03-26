jQuery(document).ready(function () {
    var NovaPoshtaSettings = (function ($) {
    
        var result = {};
        var areaInputName = $('#woocommerce_nova_poshta_shipping_method_area_name');
        var areaInputKey = $('#woocommerce_nova_poshta_shipping_method_area');
        var cityInputName = $('#woocommerce_nova_poshta_shipping_method_city_name');
        var cityInputKey = $('#woocommerce_nova_poshta_shipping_method_city');
        var warehouseInputName = $('#woocommerce_nova_poshta_shipping_method_warehouse_name');
        var warehouseInputKey = $('#woocommerce_nova_poshta_shipping_method_warehouse');
        var useFixedPrice = $("#woocommerce_nova_poshta_shipping_method_use_fixed_price_on_delivery");
        var fixedPrice = jQuery("#woocommerce_nova_poshta_shipping_method_fixed_price");
    
        var handleUseFixedPriceOnDeliveryChange = function () {
            if (useFixedPrice.prop('checked')) {
                fixedPrice.closest('tr').show();
            } else {
                fixedPrice.closest('tr').hide();
            }
        };
    
        var initUseFixedPriceOnDelivery = function () {
            useFixedPrice.change(function () {
                handleUseFixedPriceOnDeliveryChange();
            });
            handleUseFixedPriceOnDeliveryChange();
        };
    
        var initAutocomplete = function () {
            areaInputName.autocomplete({
                source: function (request, response) {
                    jQuery.ajax({
                        type: 'POST',
                        url: NovaPoshtaHelper.ajaxUrl,
                        data: {
                            action: NovaPoshtaHelper.getRegionsByNameSuggestionAction,
                            name: request.term
                        },
                        success: function (json) {
                            var data = JSON.parse(json);
                            response(jQuery.map(data, function (description, key) {
                                return {
                                    label: description,
                                    value: key
                                }
                            }));
                        }
                    })
                },
                focus: function (event, ui) {
                    areaInputName.val(ui.item.label);
                    return false;
                },
                select: function (event, ui) {
                    areaInputName.val(ui.item.label);
                    areaInputKey.val(ui.item.value);
                    clearCity();
                    clearWarehouse();
                    return false;
                }
            });
            cityInputName.autocomplete({
                source: function (request, response) {
                    jQuery.ajax({
                        type: 'POST',
                        url: NovaPoshtaHelper.ajaxUrl,
                        data: {
                            action: NovaPoshtaHelper.getCitiesByNameSuggestionAction,
                            name: request.term,
                            parent_ref: areaInputKey.val()
                        },
                        success: function (json) {
                            var data = JSON.parse(json);
                            response(jQuery.map(data, function (description, key) {
                                return {
                                    label: description,
                                    value: key
                                }
                            }));
                        }
                    })
                },
                focus: function (event, ui) {
                    cityInputName.val(ui.item.label);
                    return false;
                },
                select: function (event, ui) {
                    cityInputName.val(ui.item.label);
                    cityInputKey.val(ui.item.value);
                    clearWarehouse();
                    return false;
                }
            });
            warehouseInputName.autocomplete({
                source: function (request, response) {
                    jQuery.ajax({
                        type: 'POST',
                        url: NovaPoshtaHelper.ajaxUrl,
                        data: {
                            action: NovaPoshtaHelper.getWarehousesBySuggestionAction,
                            name: request.term,
                            parent_ref: cityInputKey.val()
                        },
                        success: function (json) {
                            var data = JSON.parse(json);
                            response(jQuery.map(data, function (description, key) {
                                return {
                                    label: description,
                                    value: key
                                }
                            }));
                        }
                    })
                },
                focus: function (event, ui) {
                    warehouseInputName.val(ui.item.label);
                    return false;
                },
                select: function (event, ui) {
                    warehouseInputName.val(ui.item.label);
                    warehouseInputKey.val(ui.item.value);
                    return false;
                }
            });
        };
    
        var clearCity = function () {
            cityInputName.val('');
            cityInputKey.val('');
        };
    
        var clearWarehouse = function () {
            warehouseInputName.val('');
            warehouseInputKey.val('');
        };
    
        var hideKeyRows = function () {
            $('.js-hide-nova-poshta-option').closest('tr').addClass('nova-poshta-option-hidden');
        };
    
        var initRating = function () {
            $('a.np-rating-link').on('click', function () {
                var link = $(this);
                $.ajax({
                    type: 'POST',
                    url: NovaPoshtaHelper.ajaxUrl,
                    data: {
                        action: NovaPoshtaHelper.markPluginsAsRated
                    },
                    success: function (json) {
                        var data = JSON.parse(json);
                        if (data.result) {
                            link.parent().text(data.message);
                        }
                    }
                });
                return true;
            });
        };
    
        result.init = function () {
            initAutocomplete();
            hideKeyRows();
            initUseFixedPriceOnDelivery();
            initRating();
        };
    
        return result;
    
    }(jQuery));
    NovaPoshtaSettings.init();
});