<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>

    <title>Invoice</title>
    <style type="text/css">
        @page {
            margin: 0;
            padding: 0;
            size: a4;
        }

        body {
            font-family: sans-serif;
            margin: 0;
            padding: 16px;
            background-color: #F9FAFC;
            font-size: 14px;
            color: #303042;
            line-height: 18px;
            -webkit-print-color-adjust: exact;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            -webkit-text-size-adjust: 100%;
            -moz-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            text-size-adjust: 100%;
            position: relative;
        }

        p,
        h2,
        h1,
        h3,
        h4,
        h5,
        h6 {
            margin: 0;
        }

        .display-table {
            display: table;
        }

        .table-cell {
            display: table-cell;
        }

        .header {
            display: table;
            width: 100%;
            color: #5E6470;
            padding: 12px;
        }

        .header .row {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .header .logo {
            width: 65px;
            height: 65px;
        }

        .header img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .text-right {
            text-align: right !important;
        }

        .pl-3 {
            padding-left: 12px;
        }

        .pt-2 {
            padding: 5px;
        }

        .pt-1-5 {
            padding-top: 2px;
        }

        .pt-1 {
            padding-top: 4px;
        }

        .pt-3 {
            padding-top: 12px;
        }

        .site-name {
            font-size: 18px;
            font-weight: 600;
            color: #303042;
            line-height: normal;
        }

        .text-gray {
            color: #5E6470;
        }

        .fz-14 {
            font-size: 14px;
            line-height: 16px
        }

        .contains {
            position: absolute;
            padding: 12px;
            background: #fff;
            left: 16px;
            right: 16px;
            bottom: 16px;
            top: 210px;
            border-radius: 16px;
        }

        .fw-400 {
            font-weight: 400 !important;
        }

        .fw-500 {
            font-weight: 500;
        }

        .fw-bold {
            font-weight: bold;
        }

        .w-full {
            width: 100%;
        }

        .payAmount {
            font-size: 20px;
            font-style: normal;
            font-weight: 700;
            line-height: 28px;
        }

        .qrCode {
            width: 61px;
            height: 60px;
        }

        .invoice-details {
            width: 100%;
            margin-top: 40px;
        }

        .invoice-details tr th {
            text-align: left;
            font-weight: normal;
            color: #5E6470;
        }

        .invoice-details tr td {
            font-weight: 500;
        }

        .items-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .items-table tr th {
            text-align: left;
            padding: 12px;
            background: #3546AE;
            color: #fff;
            font-style: normal;
            font-weight: 600;
        }

        .items-table tr {
            border-right: 0.5px solid #CFCFCF;
            border-bottom: 0.5px solid #CFCFCF;
            border-left: 0.5px solid #CFCFCF;
        }

        .items-table tr td {
            font-weight: 500;
            padding: 12px;
            background: #FFF;
        }

        .text-center {
            text-align: center !important;
        }

        .product-des {
            font-size: 10px;
            font-weight: 400;
        }

        .invoice-total {
            width: 320px;
            float: right;
            margin-top: 8px;
        }

        .clearfix {
            display: block;
            clear: both;
            content: "";
        }

        .border-top {
            border-top: 1px solid #CFCFCF;
            margin-top: 6px;
        }

        .total {
            font-weight: bold;
            font-size: 16px;
        }

        .footer {
            width: 95%;
            position: absolute;
            display: table;
            left: 20px;
            right: 32px;
            color: #303042;
            padding: 8px;
            bottom: 60px;
        }

        .footer .signature {
            border-top: 1px solid #303042;
            padding: 0 8px;
        }

        .currencyFont {
            font-family: "DejaVu Sans", sans-serif !important;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="row ">
            <div class="display-table">
                <div class="logo">
                    <img src="{{ $generaleSetting?->favicon ?? './assets/favicon.png' }}" alt="Logo" />
                </div>
                <div class="table-cell pl-3">
                    <h2 class="site-name">{{ config('app.name') }}</h2>
                    <p class="pt-1-5">{{ config('app.url') }}</p>
                    <p class="pt-1-5">{{ $generaleSetting?->email }}</p>
                    <p class="pt-1-5">{{ $generaleSetting?->mobile }}</p>
                </div>
            </div>
        </div>

        <div class="row text-right bold">
            <P> Nume Firma:2 HAND TEXTILE IMPEX SRl </P>
            <P> Reg. com.: J2011000292045</P>
            <P> CIF: RO28168013</P>
            <P> Adresă: Sat Letea Veche Com. Letea</P>
            <P> Veche, Str. Al. I. Cuza, Nr.441, Spatiul Nr.</P>
            <P> 1 , Letea Veche , Jud. Bacau</P>
            <P> IBAN: RO33BRDE040SV09518600400</P>
            <P> Banca: BRD - GROUPE SOCIETE GENERALE</P>
            <P> Tel: +40 755 511 123</P>
            <P> Email: suport@secondhub.ro</P>
            <P> Capital Social: 200</P>

        </div>
    </div>

    <div class="contains">

        <div class="display-table w-full">
            <div>
                <div class="text-gray">Livrat catre:</div>
                <p class="fw-500 pt-1">{{trans('name') }}: {{ $order->customer?->user?->name }}</p>

                <p class="fw-500 pt-1">
                    {!! nl2br($order->address->full_address) !!}
                </p>


                <div class="text-gray pt-1">{{ trans('phone').':'.$order->customer?->user?->phone }}</div>
                <p class="fw-500 pt-1">{{ trans('email').':'. $order->customer?->user?->email }}</p>
            </div>
            <div class="table-cell text-right" style="width: 110px;">
                <p class="currencyFont">PROFORMA of ({{ $generaleSetting?->currency }})</p>
                <h3 class="payAmount currencyFont">{{ showCurrency($order->payable_amount) }}</h3>
                <div class="pt-2">
                    <img class="qrCode" src="{{ $qrCodeImage }}" alt="">
                </div>
            </div>
        </div>

        <table class="invoice-details">
            <tr>
                <th>Metoda Plata</th>
                <th>Numar Proforma</th>
                <th>Data facturii</th>

            </tr>
            <tr>
                <td>{{ $order->payment_method->value }}</td>
                <td>#{{ $order->prefix . $order->order_code }}</td>
                <td>{{ now()->format('d F, Y') }}</td>

            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Produs</th>
                    <th>Denumire produs</th>

                    <th class="text-center">Numar bucati</th>
                    <th class="text-center">Cantitate</th>
                    <th class="text-right">Pret</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->products as $product)
                @php
                $price = $product->pivot->price > 0 ? $product->pivot->price : ($product->discount_price > 0 ? $product->discount_price : $product->price);
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}.</td>
                    <td>
                        <span style="text-transform: capitalize">{{ $product->name }}</span>
                        <p class="pt-1 text-gray product-des">
                            {{ Str::limit($product->short_description, 60, '...') }}
                        </p>
                    </td>

                    <td class="text-center">{{ $product->pivot->quantity }}</td>
                    <td class="text-center">{{ $product->pivot->size ?? 'N/A' }}KG</td>

                    <td class="text-right currencyFont fw-400">
                        {{ showCurrency($price * $product->pivot->quantity) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="clearfix">
            <div class="invoice-total">
                <div class="display-table w-full pt-2">
                    <p class="table-cell">Subtotal</p>
                    <p class="table-cell text-right fw-400 currencyFont">
                        {{ showCurrency($order->total_amount) }}
                    </p>
                </div>
                <div class="display-table w-full pt-2">
                    <p class="table-cell">Discount</p>
                    <p class="table-cell text-right fw-400 currencyFont">
                        {{ showCurrency($order->coupon_discount) }}
                    </p>
                </div>
                <div class="display-table w-full pt-2">
                    <p class="table-cell">Delivery Charge</p>
                    <p class="table-cell text-right fw-400 currencyFont">
                        {{ showCurrency($order->delivery_charge) }}
                    </p>
                </div>
                <div class="display-table w-full pt-2">
                    <p class="table-cell">VAT & Tax (19%)</p>
                    <p class="table-cell text-right fw-400 currencyFont">
                        {{ showCurrency($order->tax_amount) }}
                    </p>
                </div>
                <div class="display-table w-full pt-2 border-top">
                    <p class="table-cell total">Total</p>
                    <p class="table-cell text-right total currencyFont">
                        {{ showCurrency($order->payable_amount) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p class="table-cell">Va multumim pentru colaborare.</p>
        <div class="table-cell text-right">
            <!-- span class="signature">Signature</!-->
        </div>
    </div>

</body>

</html>