<label class="mt-3">Detalles de la tarjeta</label>

<div class="form-group form-row">
    <div class="col-4">
        <input class="form-control" name="payu_card" type="text" placeholder="Número de Tarjeta" required />
    </div>

    <div class="col-1">
        <input class="form-control" name="payu_cvc" type="text" placeholder="CVC" required />
    </div>

    <div class="col-1">
        <input class="form-control" name="payu_month" type="text" placeholder="MM" required />
    </div>

    <div class="col-2">
        <input class="form-control" name="payu_year" type="text" placeholder="AAAA" required />
    </div>

    <div class="col-2">
        <select class="custom-select" name="payu_network">
            <option disabled selected>Seleccione</option>
            <option value="visa">VISA</option>
            <option value="amex">AMEX</option>
            <option value="diners">DINERS</option>
            <option value="mastercard">MASTERCARD</option>
        </select>
    </div>
</div>

<div class="form-group form-row">
    <div class="col-5">
        <input class="form-control" name="payu_name" type="text" placeholder="Titular de la tarjeta" required />
    </div>
    <div class="col-5">
        <input class="form-control" name="payu_email" type="email" placeholder="email@ejemplo.com" required />
    </div>
</div>


<div class="form-group form-row">
    <div class="col">
        <small class="form-text text-mute"  role="alert" >Su pago será convertido a {{ strtoupper(config('services.payu.base_currency')) }}</small>
    </div>
</div>
