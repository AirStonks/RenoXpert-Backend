<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Created - RenoXpert</title>
    <style>
        /* Global styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100%;
            /* Ensure full height */
            display: flex;
            justify-content: center;
            /* Center horizontally */
            align-items: center;
            /* Center vertically */
        }

        .container {
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            /* Center text inside the container */
        }

        h2 {
            color: #2c3e50;
            font-size: 26px;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            color: #555;
            margin-bottom: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            text-align: left;
            /* Left-align list items */
        }

        li {
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
        }

        li strong {
            color: #2c3e50;
        }

        .footer {
            font-size: 14px;
            color: #777;
            margin-top: 30px;
        }

        .footer a {
            color: #3498db;
            text-decoration: none;
        }

        .button {
            background-color: #3498db;
            color: #fff;
            padding: 12px 25px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 20px;
        }

        .button:hover {
            background-color: #2980b9;
        }
    </style>
</head>

<body>

    <div class="container" style="padding: 1rem; background: #fcfcfc;">
        <h2>Welcome to RenoXpert, {{ $name }}!</h2>

        <p>We’re excited to have you on board. An account has been created for you with the following details:</p>

        <ul>
            <li><strong>Name:</strong> {{ $name }}</li>
            <li><strong>Email:</strong> {{ $email }}</li>
            <li><strong>User Type:</strong> {{ $userType }}</li>
            <li><strong>Temporary Password:</strong> {{ $password }}</li>
        </ul>

        <p>Please log in and change your password immediately for security purposes.</p>

        <a href="https://renoxpert.my/owner/login" class="button">Log In Now</a>

        <p class="footer">If you have any questions, feel free to <a href="mailto:developer@belive.asia">contact us</a>.
        </p>

        <p class="footer">Best regards,<br>RenoXpert Team</p>
    </div>

</body>

</html>
