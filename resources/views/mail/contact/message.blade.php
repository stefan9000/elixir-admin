<!DOCTYPE html>
<html>
<head>
    <title>Contact message</title>
</head>
<body>
<table>
    <h3>Contact message</h3>
    <ul>
        <li>Name: {{ $request->first_name }} {{ $request->last_name }}</li>
        <li>Email: {{ $request->email }}</li>
        <li>Phone: {{ $request->phone }}</li>
        <li>Message: <br/>{{ $request->message }}</li>
    </ul>
</table>
</body>
</html>
