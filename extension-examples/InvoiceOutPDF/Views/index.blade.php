@extends('admin.layouts.dashboard')
@section('content')
<?php
function formatMoney($amount)
{
    $formatter = new NumberFormatter(
        env('APP_CURRENCY_LOCALE'),
        NumberFormatter::CURRENCY
    );

    return $formatter->formatCurrency(floatval($amount), env('APP_CURRENCY'));
}
?>
<div class="container my-3 no-print">
    <div class="d-flex justify-content-between align-items-center">
        <select class="form-select w-auto" onchange="window.invoiceoutpdf?.changeLanguage(this.value)">
            <option {{$lang === 'vi' ? 'selected' : ''}} value="vi">Vietnamese</option>
            <option {{$lang === 'en' ? 'selected' : ''}} value="en">English</option>
            <option {{$lang === 'ja' ? 'selected' : ''}} value="ja">Japanese</option>
        </select>

        <button class="btn btn-outline-secondary" onclick="window.print()">
            {{ __('invoiceoutpdf::messages.print') }}
        </button>
    </div>
</div>

<div class="invoice-box container">
    <div class="row mb-4">
        <div class="col-6">
            <h3 class="fw-bold mb-1 h5">{{ __('invoiceoutpdf::messages.title') }}</h3>
            <div class="muted">{{ __('invoiceoutpdf::messages.document_no') }} #: <strong>{{$invoice->document_no}}</strong></div>
            <div class="muted">{{ __('invoiceoutpdf::messages.invoice_date') }}: {{$invoice->invoice_date}}</div>
            <div class="muted">{{ __('invoiceoutpdf::messages.due_date') }}: {{$invoice->due_date}}</div>
        </div>
        <div class="col-6 text-end">
            <h5 class="fw-bold">{{ __('invoiceoutpdf::messages.customer') }}</h5>
            <div>{{$invoice->customer_name}}</div>
            <div>{{$invoice->customer_address}}</div>
            <div class="mt-2">{{$invoice->customer_email}}</div>
            <div>{{$invoice->customer_phone}}</div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <h6 class="fw-bold">{{ __('invoiceoutpdf::messages.receiver') }}</h6>
            <div>{{$invoice->receiver_name}}</div>
            <div>{{$invoice->receiver_phone}}</div>
            <div>{{$invoice->receiver_address}}</div>
            <div class="muted">{{$invoice->receiver_note}}</div>
        </div>
        <div class="col-6 text-end">
            <h6 class="fw-bold">{{ __('invoiceoutpdf::messages.payment') }}</h6>
            <div>{{ __('invoiceoutpdf::messages.amount_paid') }}: {{formatMoney($invoice->amount_paid)}}</div>
            <div>{{ __('invoiceoutpdf::messages.payment_status') }}: {{$invoice->payment_status}}</div>
        </div>
    </div>

    <table class="table table-bordered mb-4">
        <thead class="table-light">
            <tr class="text-center">
                <th style="width:5%">#</th>
                <th class="text-start">{{ __('invoiceoutpdf::messages.name') }}</th>
                <th style="width:10%">{{ __('invoiceoutpdf::messages.buy') }}</th>
                <th style="width:10%">{{ __('invoiceoutpdf::messages.gift') }}</th>
                <th style="width:10%">{{ __('invoiceoutpdf::messages.compensation') }}</th>
                <th style="width:10%">{{ __('invoiceoutpdf::messages.conversion') }}</th>
                <th style="width:15%">{{ __('invoiceoutpdf::messages.unit_price') }}</th>
                <th style="width:15%">{{ __('invoiceoutpdf::messages.amount') }}</th>
                <th style="width:15%">{{ __('invoiceoutpdf::messages.tax') }}(%)</th>
            </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td class="text-center">{{$item->id}}</td>
                <td>{{$item->product_name}}</td>
                <td class="text-end">{{$item->buy_quantity}}</td>
                <td class="text-end">{{$item->gift_quantity}}</td>
                <td class="text-end">{{$item->compensation_quantity}}</td>
                <td class="text-end">{{$item->conversion_quantity}}</td>
                <td class="text-end">{{formatMoney($item->price)}}</td>
                <td class="text-end">{{formatMoney($item->price * $item->buy_quantity)}}</td>
                <td class="text-end">{{$item->tax}}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="row">
        <div class="col-6">
            <h6 class="fw-bold">{{ __('invoiceoutpdf::messages.note') }}</h6>
            <p class="muted mb-1">{{$invoice->order_note}}</p>
        </div>

        <div class="col-6">
            <table class="table table-sm">
                <tr>
                    <td>{{ __('invoiceoutpdf::messages.subtotal') }}</td>
                    <td class="text-end">{{formatMoney($invoice->subtotal)}}</td>
                </tr>
                <tr>
                    <td>{{ __('invoiceoutpdf::messages.tax') }}</td>
                    <td class="text-end">{{formatMoney($invoice->tax)}}</td>
                </tr>
                <tr>
                    <td>{{ __('invoiceoutpdf::messages.shipping_fee') }}</td>
                    <td class="text-end">{{formatMoney($invoice->shipping_fee)}}</td>
                </tr>
                <tr>
                    <td>{{ __('invoiceoutpdf::messages.discount') }}</td>
                    <td class="text-end">{{formatMoney($invoice->discount)}}</td>
                </tr>
                <tr class="fw-bold">
                    <td>{{ __('invoiceoutpdf::messages.total') }}</td>
                    <td class="text-end">{{formatMoney($invoice->total_adjusted)}}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="text-center mt-5 muted">
        {{ __('invoiceoutpdf::messages.footer') }}
    </div>
</div>
@endsection
