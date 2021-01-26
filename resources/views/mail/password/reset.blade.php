<!DOCTYPE html>
<html>
<head>
    <title>Password reset</title>
</head>
<body>
<table>
    <h3>Password reset request</h3>
    <p>A password reset has been requested for your account, if this wasn't you, ignore this email.</p>
    <p>In order to reset your password, please click <a href="{{ route('password_reset_edit', ['token' => $token]) }}">here</a>.</p>
    <p>If the above link does not work, please copy this link into your browser: <br/>{{ route('password_reset_edit', ['token' => $token]) }}</p>
</table>
</body>
</html>
