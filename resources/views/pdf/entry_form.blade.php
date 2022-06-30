<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>INGRESO DE BIENES CONSUMIBLES</title>
  <style>
    body{
      font: .9rem Arial, sans-serif;
    }
    table{
      border-collapse: collapse;
      width: 100%;
    }
    table td{
      padding: 5px 3px;
    }
    img{
      max-width: 100%;
    }
    .logo{
      width: 200px;
      height: auto;
    }
    .logo-table th:nth-child(3){
      font-size: .8rem;
    }
    .header-table th{
      text-align: left;
    }
    .header-table .value{
      font-weight:normal;
    }
    .header-table, .data-table, .footer-table{
      margin-top: 10px;
    }
    .data-table th, .data-table td{
      border: 1px solid #000;
    }
    .data-table th{
      font-weight: bold;
      font-size: .8rem;
      text-align: center;
    }
    .data-table td{
      font-size: .7rem;
      text-align:right;
    }
    .footer-table th, .footer-table td{
      border: 1px solid #000;
      font-size: .8rem;
    }
    .footer-table tr:nth-child(2) td{
      text-align: center;
      padding-bottom: 60px;
    }
  </style>
</head>
<body>
<table class="logo-table">
  <tr>
    <th>
      <img class="logo" src="{{public_path('/images/logo.jpg')}}">
    </th>
    <th>
      Formulario <br>
      INGRESO DE BIENES CONSUMIBLES
    </th>
    <th>
      {!! $header !!}<br>
      Pág No. 1 de 1
    </th>
  </tr>
</table>
<table class="header-table">
  <tr>
    <th>
      Proveedor: <span class="value">{{ $provider["social_reason"] }}</span>
      <br>
      Nº y fecha de factura: <span class="value">{{ $invoice_date  }}</span>

      <br>
      Nº de proceso de compra: <span class="value">{{ $buy_process }}</span>
      <br>
      Nº de orden de compra/Contrato: <span class="value">{{ $buy_order }}</span>
      <br>
    </th>
    @php
      $curyear = date("Y");
    @endphp

    <th>
      T/C: <span class="value">{{ $exchange_rate }}</span>
      <br>
      Nº de NI: <span class="value">{{ $correlative }}</span>
      <br>
      Fecha: <span class="value">{{ $date }}</span>
      <br>
      Rº mat: <span class="value">{{ $reg }}</span>
      <br>
    </th>
  </tr>
</table>
<table class="data-table">
  <thead>
  <tr>
    <th>Item</th>
    <th>Código</th>
    <th>Cantidad</th>
    <th>U.M.</th>
    <th>Descripción</th>
    <th>Partida</th>
    <th>Costo Unitario</th>
    <th>Costo total</th>
  </tr>
  </thead>
  <tbody>
  @php
    $count = 1;
    $grandtotal = 0;
  @endphp
  @foreach($entriesdetails as $detail)
  <tr>
    <td>{{ $count  }}</td>
    <td>{{ $detail["article"]["code"] }}</td>
    <td>{{ $detail["qty"] }}</td>
    <td>{{ $detail["article"]["unit"]["abr"] }}</td>
    <td>{{ $detail["article"]["description"] }}</td>
    <td>{{ $detail["article"]["account"]["account"] }}</td>
    <td>{{ $detail["unit_price"]}}</td>
    @php
      $total = $detail["qty"] * $detail["unit_price"];
      $grandtotal += $total;
      $grandtotaltext = number_format($grandtotal, 2, '.', '');
    @endphp
    <td>{{ number_format($total, 2, '.', ',') }}</td>
  </tr>
    @php
      $count += 1;
      $helper = new App\Helpers\Helper;
    @endphp
  @endforeach
  <tr>
    <td colspan="7" >TOTAL</td><td>{{ number_format($grandtotal, 2, '.', ',') }}</td>
  </tr>

  <tr>
    <td colspan="8">SON: {{ $helper->getFullLiteralMoney($grandtotaltext) }}</td>
  </tr>
  </tbody>
</table>
<table class="footer-table">
  <tr>
    <td colspan="3">
      OBSERVACIONES:
    </td>
  </tr>
  <tr>
    <td>
      ELABORADO POR<br>
    </td>
    <td>
      REVISADO POR<br>
    </td>
    <td>
      AUTORIZADO POR<br>
    </td>
  </tr>
</table>
</body>
</html>
