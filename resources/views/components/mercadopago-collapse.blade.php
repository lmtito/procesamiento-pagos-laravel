<label class="mt-3">Detalles del comprador</label>
<div class="form-group form-row">
    <div class="col-5">
        <input class="form-control" type="email" data-checkout="cardholderEmail" placeholder="test@test.com" name="email">
    </div>

    <div class="col-2">
        <select class="custom-select" data-checkout="docType"></select>
    </div>

    <div class="col-3">
        <input class="form-control" type="text" data-checkout="docNumber" placeholder="Documento">
    </div>
</div>

<label class="mt-2">Detalles de la tarjeta</label>
<div class="form-group form-row">
    <div class="col-5">
        <input class="form-control" type="text" id="cardholderName" data-checkout="cardholderName" placeholder="Titular de la tarjeta">
    </div>
</div>

<div class="form-group form-row">
    <div class="col-5">
        <input class="form-control" type="text" id="cardNumber" data-checkout="cardNumber" placeholder="Número de Tarjeta" onselectstart="return false" onpaste="return false" oncopy="return false" oncut="return false" ondrag="return false" ondrop="return false" autocomplete=off>
    </div>

    <div class="col-2">
        <input class="form-control" type="text" id="securityCode" data-checkout="securityCode" placeholder="CVC" onselectstart="return false" onpaste="return false" oncopy="return false" oncut="return false" ondrag="return false" ondrop="return false" autocomplete=off>
    </div>

    <div class="col-1"></div>

    <div class="col-1">
        <input class="form-control" type="text" data-checkout="cardExpirationMonth" placeholder="MM" id="cardExpirationMonth" onselectstart="return false" onpaste="return false" oncopy="return false" oncut="return false" ondrag="return false" ondrop="return false" autocomplete=off>
    </div>

    <div class="col-1">
        <input class="form-control" type="text" data-checkout="cardExpirationYear" placeholder="AA" id="cardExpirationYear" onselectstart="return false" onpaste="return false" oncopy="return false" oncut="return false" ondrag="return false" ondrop="return false" autocomplete=off>
    </div>
</div>

<div class="form-group form-row">
    <div class="col-5">
        <label for="issuer">Banco emisor</label>
        <select id="issuer" name="issuer" data-checkout="issuer"></select>
    </div>

    <div class="col-2">
        <label for="installments">Cuotas</label>
        <select type="text" id="installments" name="installments"></select>
    </div>
</div>

<div class="form-group form-row">
    <div class="col">
        <small class="form-text text-mute"  role="alert" >Su pago será convertido a {{ strtoupper(config('services.mercadopago.base_currency')) }}</small>
    </div>
</div>

<div class="form-group form-row">
    <div class="col">
        <small class="form-text text-danger" id="paymentErrors" role="alert"></small>
    </div>
</div>

<input type="hidden" id="paymentMethodId" name="payment_method_id" />
<input type="hidden" id="cardToken" name="card_token">

@push('scripts')
<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>

<script>
    const mercadoPago = window.Mercadopago;
    mercadoPago.setPublishableKey('{{ config('services.mercadopago.key') }}');
    mercadoPago.getIdentificationTypes();
</script>

<script>
    // Obtener método de pago de la tarjeta
    document.getElementById('cardNumber').addEventListener('change', guessPaymentMethod);

    function guessPaymentMethod(e) {
        let cardNumber = document.getElementById("cardNumber").value;
        if (cardNumber.length >= 6) {
            mercadoPago.getPaymentMethod(
                { "bin": cardNumber.substring(0,6) }, setPaymentMethod);
        }
    };

    function setPaymentMethod(status, response) {
        if (status == 200) {
            let paymentMethod = response[0];
            document.getElementById('paymentMethodId').value = paymentMethod.id;

            if (paymentMethod.additional_info_needed.includes("issuer_id")) {
                getIssuers(paymentMethod.id);
            } else {
                getInstallments(
                    paymentMethod.id,
                    document.getElementByName('value').value
                );
            }
        } else {
            // alert(`Error de información del método de pago: ${response}`);
            let errors = document.getElementById("paymentErrors");
            errors.textContent = response.cause[0].description;
        }
    }
</script>

<script>
    // Obtener banco emisor
    function getIssuers(paymentMethodId) {
        mercadoPago.getIssuers(
            paymentMethodId,
            setIssuers
        );
    }

    function setIssuers(status, response) {
        if (status == 200) {
            let issuerSelect = document.getElementById('issuer');
            response.forEach( issuer => {
                let opt = document.createElement('option');
                opt.text = issuer.name;
                opt.value = issuer.id;
                issuerSelect.appendChild(opt);
            });

            getInstallments(
                document.getElementById('paymentMethodId').value,
                document.getElementById('value').value,
                issuerSelect.value
            );
        } else {
            // alert(`Error de información del método del emisor: ${response}`);
            let errors = document.getElementById("paymentErrors");
            errors.textContent = response.cause[0].description;
        }
    }
</script>

<script>
    // Obtener cantidad de cuotas
    function getInstallments(paymentMethodId, transactionAmount, issuerId){
        mercadoPago.getInstallments({
            "payment_method_id": paymentMethodId,
            "amount": parseFloat(transactionAmount),
            "issuer_id": issuerId ? parseInt(issuerId) : undefined
        }, setInstallments);
    }

    function setInstallments(status, response){
        if (status == 200) {
            document.getElementById('installments').options.length = 0;
            response[0].payer_costs.forEach( payerCost => {
                let opt = document.createElement('option');
                opt.text = payerCost.recommended_message;
                opt.value = payerCost.installments;
                document.getElementById('installments').appendChild(opt);
            });
        } else {
            // alert(`Error de información del método de cuotas: ${response}`);
            let errors = document.getElementById("paymentErrors");
            errors.textContent = response.cause[0].description;
        }
    }
</script>

<script>
    // Crear el token de la tarjeta
    const mercadoPagoForm = document.getElementById("paymentForm");

    mercadoPagoForm.addEventListener('submit', function(e) {
        if (mercadoPagoForm.elements.payment_platform.value === "{{ $paymentPlatform->id }}") {
            e.preventDefault();

            mercadoPago.createToken(mercadoPagoForm, function(status, response) {
                if (status != 200 && status != 201) {
                    // alert("Verificar datos completados!\n"+JSON.stringify(response, null, 4));
                    let errors = document.getElementById("paymentErrors");
                    errors.textContent = response.cause[0].description;
                } else {
                    const cardToken = document.getElementById("cardToken");
                    cardToken.value = response.id;
                    mercadoPagoForm.submit();
                }
            });
        }
    });
</script>
@endpush
