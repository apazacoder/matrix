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
      INVENTARIO DE BIENES CONSUMIBLES EXISTENTES EN ALMACÉN OFICINA CENTRAL LA PAZ<br>
      AL 31 de diciembre de 2021<br>
      (EXPRESADO EN BOLIVIANOS)
    </th>
  </tr>
</table>
<table class="data-table">
  <thead>
  <tr>
    <th>No.</th>
    <th>Codificación</th>
    <th>U.M.</th>
    <th>Descripción</th>
    <th>Cantidad según reporte</th>
    <th>Costo unitario</th>
    <th>Costo según reporte</th>
  </tr>
  </thead>
  <tbody>
  @php
    $count = 1;
    $grandtotal = 0;
  @endphp
  @foreach($items  as $detail)
  <tr>
    <td>{{ $count }}</td>
    <td>{{ $detail["article"]["code"] }}</td>
    <td>{{ $detail["article"]["unit"]["abr"] }}</td>
    <td style="text-align:center">{{ $detail["article"]["description"] }}</td>
    <td>{{ $detail["qty"] }}</td>
    <td>{{ $detail["unit_price"] }}</td>
    @php
      $total = $detail["qty"] * $detail["unit_price"];
        $total = $count;
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
    <td colspan="6" >TOTAL</td><td>{{ number_format($grandtotal, 2, '.', ',') }}</td>
  </tr>

  <tr>
    <td colspan="7">SON: {{ $helper->getFullLiteralMoney(number_format($grandtotal, 2, '.', '')) }} items</td>
  </tr>
  </tbody>
</table>
<table class="footer-table first" >
  <tr>
    <td>
      PERSONAL DE ALMACENES - FASE II LA PAZ<br>
    </td>
    <td>
      VºBº ADMINISTRACIÓN<br>
    </td>
  </tr>
</table>
</body>
</html>
