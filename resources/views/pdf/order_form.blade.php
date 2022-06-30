<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>PEDIDO Y ENTREGA DE BIENES CONSUMIBLES</title>
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
    .footer-table tr:nth-child(1) td{
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
      PEDIDO Y ENTREGA DE BIENES CONSUMIBLES
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
      Nombre del solicitante: <span class="value">
        {{ $user["first_name"] }} {{ $user["first_surname"] }} {{ $user["second_surname"] }}
      </span>
      <br>
      Cargo del solicitante: <span class="value">
        {{ $user["position"] }}
      </span>
      <br>
      Fecha de pedido y entrega: <span class="value">{{ $order_date }}</span>
      <br>
      Centro de costo: <span class="value">{{ $center["name"] }}</span>
      <br>
      T/C: <span class="value">{{ $exchange_rate }}</span>
      <br>
    </th>
    @php
      $curyear = date("Y");
    @endphp

    <th>
      Nº DE: <span class="value">{{ $correlative }} / {{ $curyear }}</span>
      <br>
      C.I: <span class="value">{{ $user["ci"] }}</span>
      <br>
      Fase: <span class="value">{{ $phase }}</span>
      <br>
      Distrito: <span class="value">{{ $district }}</span>
      <br>
      Destino: <span class="value">{{ $destiny }}</span>
      <br>
    </th>
  </tr>
</table>
<table class="data-table">
  <thead>
  <tr>
    <th rowspan="2">Item</th>
    <th rowspan="2">Código</th>
    <th rowspan="2">U.M.</th>
    <th rowspan="1" colspan="2">Cantidad</th>
    <th rowspan="2">Descripción</th>
{{--    <th rowspan="2">Costo Unitario</th>--}}
{{--    <th rowspan="2">Costo total</th>--}}
  </tr>
  <tr>
    <th>Pedida</th>
    <th>Autorizada</th>
  </tr>
  </thead>
  <tbody>
  @php
    $count = 1;
    $grandtotal = 0;
  @endphp
  @foreach($ordersdetails as $detail)
  <tr>
    <td>{{ $count  }}</td>
    <td>{{ $detail["article"]["code"] }}</td>
    <td>{{ $detail["article"]["unit"]["abr"] }}</td>
    <td>{{ $detail["qty"] }}</td>
    <td></td>
    <td>{{ $detail["article"]["description"] }}</td>
{{--    <td></td>--}}
{{--    <td>{{ $detail["unit_price"]}}</td>--}}
    @php
      $total = $detail["qty"] * $detail["unit_price"];
        $total = $count;
      $grandtotal += $total;
      $grandtotaltext = number_format($grandtotal, 2, '.', '');
    @endphp
{{--    <td>{{ number_format($total, 2, '.', ',') }}</td>--}}
  </tr>
    @php
      $count += 1;
      $helper = new App\Helpers\Helper;
    @endphp
  @endforeach
  <tr>
    <td colspan="5" >TOTAL</td><td>{{ number_format($grandtotal, 2, '.', ',') }}</td>
  </tr>

  <tr>
    <td colspan="6">SON: {{ ($count-1) }} items</td>
  </tr>
  </tbody>
</table>
<table class="footer-table first" >
  <tr>
    <td>
      SOLICITADO POR<br>
    </td>
    <td>
      VºBº INMEDIATO SUPERIOR<br>
    </td>
    <td>
      AUTORIZADO POR<br>
    </td>
    <td>
      ENTREGADO POR<br>
    </td>
    <td>
      RECIBIDO POR<br>
    </td>
  </tr>
</table>
</body>
</html>
