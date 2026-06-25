<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify Email</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">

                <table width="600" cellpadding="20" cellspacing="0" style="background: #ffffff; border-radius: 8px;">
                    <tr>
                        <td align="center" style="padding-bottom: 10px;">
                            <img src="{{ url('images/hhp-logo.png') }}" 
                                alt="Logo" 
                                width="120" 
                                style="display: block;">
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <h2 style="margin: 0;">Welcome, {{ $user->first_name }} 👋</h2>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p>
                                Thank you for registering. Please verify your email address by clicking the button below.
                            </p>

                            <p style="text-align: center; margin: 30px 0;">
                                <a href="{{ $url }}"
                                   style="background-color: #0000FF; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px;">
                                    Verify Email
                                </a>
                            </p>

                            <p>
                                If you did not create an account, no further action is required.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="font-size: 12px; color: #888;">
                            © {{ date('Y') }} Human Health Project. All rights reserved.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>