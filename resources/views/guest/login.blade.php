<!DOCTYPE html>
<html>
<head>
    <title>Login | Elixir Events</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<section class="section-login">
    <img class="logo-login" src="/images/logo.svg" alt="Logo">
    <div class="form-holder">
        <h2>Sign in</h2>
        <h6>Elixir Events Admin Dashboard</h6>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label>E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required/>
            <label>Password</label>
            <input type="password" name="password" required/>
            <button type="submit">Login</button>
        </form>
    </div>
</section>
</body>
</html>
