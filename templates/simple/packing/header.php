<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo $this->get_packing_title(); ?></title>
    <style>
        @page {
            margin-top: 1cm;
            margin-bottom: 3cm;
            margin-left: 2cm;
            margin-right: 2cm
        }

        * {
            line-height: 1.5em
        }

        body {
            background: #fff;
            color: #000;
            margin: 0cm;
            font-family: 'Open Sans', sans-serif;
            font-size: 9pt;
            line-height: 100%;
            line-height: 1.3rem
        }

        h1,
        h2,
        h3,
        h4 {
            font-weight: 700;
            margin: 0
        }

        h1 {
            font-size: 16pt;
            margin: 5mm 0
        }

        h2 {
            font-size: 14pt
        }

        h3,
        h4 {
            font-size: 9pt
        }

        ol,
        ul {
            list-style: none;
            margin: 0;
            padding: 0
        }

        li,
        ul {
            margin-bottom: .75em
        }

        p {
            margin: 0;
            padding: 0
        }

        p+p {
            margin-top: 1.25em
        }

        a {
            border-bottom: 1px solid;
            text-decoration: none
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
            page-break-inside: always;
            border: 0;
            margin: 0;
            padding: 0
        }

        th,
        td {
            vertical-align: top;
            text-align: left
        }

        table.container {
            width: 100%;
            border: 0
        }

        tr.no-borders,
        td.no-borders {
            border: 0 !important;
            border-top: 0 !important;
            border-bottom: 0 !important;
            padding: 0 !important;
            width: auto
        }

        div.bottom-spacer {
            clear: both;
            height: 8mm
        }

        table.head {
            margin-bottom: 0mm
        }

        td.header img {
            max-height: 3cm;
            width: auto
        }

        td.header {
            font-size: 16pt;
            font-weight: 700
        }

        td.shop-info {
            width: 40%
        }

        .document-type-label {
            text-transform: uppercase
        }

        table.order-data-addresses {
            width: 100%;
            margin-bottom: 10mm
        }

        td.order-data {
            width: 40%
        }

        .invoice .shipping-address {
            width: 30%
        }

        .packing-slip .billing-address {
            width: 30%
        }

        td.order-data table th {
            font-weight: 400;
            padding-right: 2mm
        }

        table.order-details {
            width: 100%;
            margin-bottom: 0mm
        }

        .quantity,
        .price {
            width: 20%
        }

        .order-details tr {
            page-break-inside: always;
            page-break-after: auto
        }

        .order-details td,
        .order-details th {
            border-bottom: 1px #ccc solid;
            border-top: 1px #ccc solid;
            padding: .375em
        }

        .order-details th {
            font-weight: 700;
            text-align: left
        }

        .order-details thead th {
            color: #fff;
            background-color: #000;
            border-color: #000
        }

        .order-details tr.bundled-item td.product {
            padding-left: 5mm
        }

        .order-details tr.product-bundle td,
        .order-details tr.bundled-item td {
            border: 0
        }

        .order-details tr.bundled-item.hidden {
            display: none
        }

        dl {
            margin: 4px 0
        }

        dt,
        dd,
        dd p {
            display: inline;
            font-size: 7pt;
            line-height: 7pt
        }

        dd {
            margin-left: 5px
        }

        dd:after {
            content: "\A";
            white-space: pre
        }

        .wc-item-meta {
            margin: 4px 0;
            font-size: 7pt;
            line-height: 7pt
        }

        .wc-item-meta p {
            display: inline
        }

        .wc-item-meta li {
            margin: 0;
            margin-left: 5px
        }

        .document-notes,
        .customer-notes {
            margin-top: 5mm
        }

        table.totals {
            width: 100%;
            margin-top: 5mm
        }

        table.totals th,
        table.totals td {
            border: 0;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc
        }

        table.totals th.description,
        table.totals td.price {
            width: 50%
        }

        table.totals tr.order_total td,
        table.totals tr.order_total th {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-weight: 700
        }

        table.totals tr.payment_method {
            display: none
        }

        #footer {
            position: absolute;
            bottom: -2cm;
            left: 0;
            right: 0;
            height: 2cm;
            text-align: center;
            border-top: .1mm solid gray;
            margin-bottom: 0;
            padding-top: 2mm
        }

        .pagenum:before {
            content: counter(page)
        }

        .pagenum,
        .pagecount {
            font-family: sans-serif
        }
    </style>
</head>

<body>