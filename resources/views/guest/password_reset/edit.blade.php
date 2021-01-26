<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <title>Password Reset - Elixir Events</title>
</head>
<body>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100vw;
            background-color: #2a363b;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            font-size: 16px;
        }

        .heading {
            font-size: 1.4em;
            color: #fff;
        }

        .success {
            color: #4CAF50;
        }

        form {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            width: 400px;
            max-width: 100%;
        }

        label {
            color: #fff;
            font-size: 0.8em;
        }

        input {
            padding: .75rem 0.5rem;
            margin-bottom: 0.5rem;
            border: 0;
            border-radius: 0.15rem;
            font-size: $h7: 1em;
            color: #2a363b
        }



        .submit {
            margin-top: 2rem;
            background-color: #4CAF50;
            color: #fff;
            border: 0;
            border-radius: 0.15rem;
            padding: 0.75rem;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
    @if (!$reset_success)
        <h1 class="heading">Please type in your new password into the form below.</h1>
        <form class="resetForm" method="POST">
            @csrf
            @method('PATCH')
            <label>New password</label>
            <input type="password" name="password" required/>
            <label>Confirm new password</label>
            <input type="password" name="password_confirmation" required/>
            <button class="submit" type="submit">Reset password</button>
        </form>
        <p style="color: red;">{{ $errors->first('password') }}</p>
    @else
        <h1 class="success">You have successfully reset your password, you may log in now.</h1>
    @endif
</body>
</html>
