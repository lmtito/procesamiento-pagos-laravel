@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Realizar un pago</div>

                <div class="card-body">
                    <form action="{{ route('pay') }}" method="POST" id="paymentForm">
                        @csrf
                        <div class="row">
                            <div class="col-auto">
                                <label>¿Cuánto desea pagar?</label>
                                <input type="number" min="5" step="0.01" class="form-control" id="value" name="value" value="{{ mt_rand(500, 100000) / 100 }}" required>
                                <small class="form-text text-muted">
                                    Utilice valores con 2 cifras decimales separadas por punto.
                                </small>
                            </div>

                            <div class="col-auto">
                                <label>Moneda</label>
                                <select class="custom-select" name="currency" required>
                                    <option disabled selected>- Seleccione -</option>
                                    @foreach ($currencies as $currency)
                                        <option {{old('currency') == $currency->iso ? 'selected' : ''}} value="{{ $currency->iso }}">
                                            {{ strtoupper($currency->iso) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col">
                                <label>Seleccione la Plataforma de Pago</label>
                                <div class="form-group" id="toggler">
                                    <div class="btn-group btn-group-toggle" data-togle="buttons">
                                        @foreach ($paymentPlatforms as $paymentPlatform)
                                            <label class="btn btn-outline-secondary rounded m-2 p-1" data-target="#{{ $paymentPlatform->name }}Collapse" data-toggle="collapse">
                                                <input type="radio" name="payment_platform" value="{{ $paymentPlatform->id }}" required>
                                                <img class="img-thumbnail" src="{{ asset($paymentPlatform->image) }}">
                                            </label>
                                        @endforeach
                                    </div>
                                    @foreach ($paymentPlatforms as $paymentPlatform)
                                        <div id="{{ $paymentPlatform->name }}Collapse" class="collapse" data-parent="#toggler">
                                            @includeIf('components.' . strtolower($paymentPlatform->name) . '-collapse')
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" id="payButton" class="btn btn-primary btn-lg">Pagar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
