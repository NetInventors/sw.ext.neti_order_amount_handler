;
if ('undefined' === typeof Neti) {
    var Neti;
}

Neti = Neti || {};

$.extend(Neti, {
    'OrderAmountHandler': {
        'hidePaymentSelect': function () {
            if (typeof netiHidePaymentSelect !== 'undefined' && netiHidePaymentSelect) {
                var $paymentMethodList = $('.payment--method-list');

                $paymentMethodList.find('.alert.is--info.is--hidden').removeClass('is--hidden');
                $paymentMethodList.find('.panel--body').addClass('is--hidden');
            }
        }
    }
});

Neti.OrderAmountHandler.hidePaymentSelect();

$.subscribe('plugin/swShippingPayment/onInputChanged', Neti.OrderAmountHandler.hidePaymentSelect);
