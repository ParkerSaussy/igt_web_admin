<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>LesGo</title>

    <!-- Responsive Metatag -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"> --}}
</head>

<body style="margin:0; padding: 30px; font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 400; color: #131B4C; background-color: #ffffff;">
    <table style="width:100%; border-spacing:0; margin: 0 auto 30px; background-color: #ffffff; font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 400; color: #131B4C; border:1px solid #131B4C;" cellpadding="0" cellspacing="0">
        <tbody>
           
           
            <tr style="background-color: #2AB26E;">
                <th align="center" colspan="7" style="padding:5px; border:1px solid #131B4C;">COSTS</th>
            </tr>
            <tr style="background-color: #D3DCE4;">
                <th rowspan="2" style="padding:5px; border:1px solid #131B4C;">Description</th>
                <th rowspan="2" style="padding:5px; border:1px solid #131B4C;">Date</th>
                <th rowspan="2" style="padding:5px; border:1px solid #131B4C;">Paid by</th>
                <th rowspan="2" style="padding:5px; border:1px solid #131B4C;">Price</th>
                <th colspan="{{ count($guest) }}" style="padding:5px; border:1px solid #131B4C;">Paid for</th>
            </tr>
           
            <tr style="background-color: #D3DCE4;">
                @foreach ($guest as $guests)
                <th style="padding:5px; border:1px solid #131B4C;">{{$guests->name}}</th>
                @endforeach
            </tr>
           
            @foreach ($cost as $costs)
            <tr>
                <td style="padding:5px; border:1px solid #131B4C;">{{$costs->name}} </td>
                <td style="padding:5px; border:1px solid #131B4C;">{{$costs->expenseOn}}</td>
                <td style="padding:5px; border:1px solid #131B4C;">{{$costs->first_name}} {{$costs->last_name}}</td>
                <td align="right" style="padding: 5px; border:1px solid #131B4C; background-color: #bdfcdd;">{{$costs->amount}}</td>
                @foreach ($guest as $guests)
                <td align="right" style="padding: 5px; border:1px solid #131B4C; background-color: #ffbbbb;">@if (isset($costs->{$guests->name}))
                    {{ $costs->{$guests->name} }}
                @endif</td>
                
                @endforeach
            </tr>
            @endforeach
            
           
        </tbody>
    </table>
    <table style="width:100%; border-spacing:0; margin: 0 auto 30px; background-color: #ffffff; font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 400; color: #131B4C; border:1px solid #131B4C;" cellpadding="0" cellspacing="0">
        <tbody>
            <tr style="background-color: #2AB26E;">
                <th align="center" colspan="7" style="padding:5px; border:1px solid #131B4C;">TRANSFERS</th>
            </tr>
            <tr style="background-color: #D3DCE4;">
                <th rowspan="2" style="padding:5px; border:1px solid #131B4C;">Description</th>
                <th rowspan="2" style="padding:5px; border:1px solid #131B4C;">Date</th>
                <th rowspan="2" style="padding:5px; border:1px solid #131B4C;">Paid by</th>
                <th rowspan="2" style="padding:5px; border:1px solid #131B4C;">Price</th>
                <th colspan="{{ count($guest) }}" style="padding:5px; border:1px solid #131B4C;">Paid for</th>
            </tr>
            <tr style="background-color: #D3DCE4;">
                @foreach ($guest as $guests)
                <th style="padding:5px; border:1px solid #131B4C;">{{$guests->name}}</th>
                @endforeach
            </tr>
            @foreach ($transfer as $transfers)
            <tr>
                <td style="padding:5px; border:1px solid #131B4C;">{{$transfers->Sender}} to {{$transfers->Receiver}}</td>
                <td style="padding:5px; border:1px solid #131B4C;">{{$transfers->TransferOn}}</td>
                <td style="padding:5px; border:1px solid #131B4C;">{{$transfers->Sender}}</td>
                <td align="right" style="padding: 5px; border:1px solid #131B4C; background-color: #bdfcdd;">{{$transfers->amount}}</td>
              
                @foreach ($guest as $guests)
                @if (($transfers->Receiver === $guests->name))
                    <td align="right" style="padding: 5px; border: 1px solid #131B4C; background-color: #ffbbbb;">{{$transfers->amount}}</td>
                @else
                    <td align="right" style="padding: 5px; border: 1px solid #131B4C;"></td>
                @endif
            @endforeach
                
            </tr>
            @endforeach
        </tbody>
    </table>
    {{-- <table style="width:100%; border-spacing:0; margin: 0 auto 30px; background-color: #ffffff; font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 400; color: #131B4C; border:1px solid #131B4C;" cellpadding="0" cellspacing="0">
        <tbody>
            <tr style="background-color: #2AB26E;">
                <th align="center" colspan="7" style="padding:5px; border:1px solid #131B4C;">SUMMARY</th>
            </tr>
            <tr style="background-color: #D3DCE4;">
                <th style="padding:5px; border:1px solid #131B4C;">Description</th>
                <th style="padding:5px; border:1px solid #131B4C;">Date</th>
                <th style="padding:5px; border:1px solid #131B4C;">Divyesh Zanzmeria</th>
                <th style="padding:5px; border:1px solid #131B4C;">People Should Probably Be First Navigation At Bottom</th>
                <th style="padding:5px; border:1px solid #131B4C;">Person 3 </th>
            </tr>
            <tr>
                <td style="padding:5px; border:1px solid #131B4C;">Total costs</td>
                <td style="padding:5px; border:1px solid #131B4C;">Jun 8, 2023</td>
                <td align="right" style="padding: 5px; border:1px solid #131B4C; background-color: #ffbbbb;">$33670.00</td>
                <td align="right" style="padding: 5px; border:1px solid #131B4C; background-color: #ffbbbb;">$33670.00</td>
                <td align="right" style="padding: 5px; border:1px solid #131B4C; background-color: #ffbbbb;">$33670.00</td>
            </tr>
            <tr>
                <td style="padding:5px; border:1px solid #131B4C;">Balance</td>
                <td style="padding:5px; border:1px solid #131B4C;">Jun 8, 2023</td>
                <td align="right" style="padding: 5px; border:1px solid #131B4C; background-color: #4CDA93;"><b>owes $0.00</b></td>
                <td align="right" style="padding: 5px; border:1px solid #131B4C; background-color: #D94242;"><b>owes $32670.00</b></td>
                <td align="right" style="padding: 5px; border:1px solid #131B4C; background-color: #4CDA93;"><b>is owed $32670.00</b></td>
            </tr>
            <tr style="background-color: #D3DCE4;">
                <td align="center" colspan="7" style="padding:5px; border:1px solid #131B4C;"><b>TOTAL COSTS: $101010.00</b></td>
            </tr>
        </tbody>
    </table> --}}
    <table style="width:100%; border-spacing:0; margin: 0 auto 30px; background-color: #ffffff; font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 400; color: #131B4C; border:1px solid #131B4C;" cellpadding="0" cellspacing="0">
        <tbody>
            <tr style="background-color: #2AB26E;">
                <th align="center" colspan="2" style="padding:5px; border:1px solid #131B4C;">RESOLUTIONS</th>
            </tr>
            <tr>
                @foreach ($resolution as $resolutions)
                <td align="left" style="padding: 5px; border:1px solid #131B4C;">{{$resolutions->Debtor}} owes {{$resolutions->Creditor}}</td>
                <td align="right" style="padding: 5px; border:1px solid #131B4C; background-color: #ffbbbb;">{{$resolutions->amount}}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
</body>

</html>