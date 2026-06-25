<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact Email</title>
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
                            <p>New Contact Message</p>
                        </td>
                    <tr>
                        <td align="center">
                            <p>From: {{ $data['first_name'] }} {{ $data['last_name'] }} - {{  $data['email'] }} </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <p>Country: {{ $data['country'] }} </p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p>
                                {{ $data['message'] }}
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