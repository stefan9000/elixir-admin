<!DOCTYPE html>
<html>
<head>
    <title>Tickets Purchased</title>
</head>
<body>
<table>
    <thead>
    <tr>
        <th>Buyer</th>
        <th>Price</th>
        <th>QR</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($tickets as $k=>$v)
            <tr>
                <td>{{ $v->user->first_name }}{{ $v->user->last_name }}</td>
                <td>{{ $v->price }}</td>
                <td>
                    <img src="{!!$message->embedData(QrCode::format('png')->generate($v->code), 'ticket_'. ($k + 1) .'.png', 'image/png')!!}">
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
