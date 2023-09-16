<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.cdnfonts.com/css/product-sans?styles=15767" rel="stylesheet">

    <style>
        @media only screen and (max-width: 600px) {
        .inner-body {
        width: 100% !important;
        }

        .footer {
        width: 100% !important;
        }
        }

        @media only screen and (max-width: 500px) {
        .button {
        width: 100% !important;
        }
        }

        @media only screen and (min-width: 600px) {
            .logo-link img{
                margin-right: 442px;
            }

            .logo-link span{
                margin-right: 418px;
            }
        }

        body,
        a,
        p,
        span
        {
            font-family: 'Product Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            font-weight: 400;
        }
        .im {
            color: inherit;
        }
    </style>
</head>

<body style="box-sizing: border-box; position: relative; -webkit-text-size-adjust: none; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;">

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;">
        <tbody>
            <tr>
                <td align="center" style="box-sizing: border-box; position: relative;">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;">
                    <tbody>
                        <tr style="text-align: center">
                            <td class="header" style="box-sizing: border-box; position: relative; padding: 25px 0; text-align: center;">
                                <a target="_blank" rel="noopener noreferrer" href="{{ config('env.spa_url') }}" style="box-sizing: border-box; position: relative; color: #3d4852; font-size: 19px; font-weight: bold; text-decoration: none; display: inline-block;" class="logo-link">
                                    @if($user && $hasCustomSender)
                                        @if($user->setting['business_logo'])
                                            <img src="{{ $user->setting['business_logo'] }}" class="logo" alt="..." style="width: 120px; margin: auto">
                                        @else
                                            <span class="logo" style="box-sizing: border-box; position: relative; width: 200px; height: auto; color: #0000006b; font-size: 24px; text-align: center">
                                                {{ $user->name }}
                                            </span>
                                        @endif
                                    @else
                                        <img src="{{ asset('assets/images/logo.png') }}" class="logo" alt="..." style="width: 120px; margin: auto">
                                    @endif
                                </a>
                            </td>
                        </tr>

<!-- Email Body -->
                        <tr>
                            <td class="body" width="100%" cellpadding="0" cellspacing="0" style="box-sizing: border-box; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;">
                                <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; background-color: #efefef52; border-color: #e8e5ef; border-width: 1px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 10px; width: 570px; font-weight: 500; color: #2e3d45; border-radius: 5px;">
<!-- Body content -->
                                <tbody>
                                    <tr>
                                        <td class="content-cell" style="box-sizing: border-box; position: relative; max-width: 100vw; padding: 32px;">
                                            <p style="box-sizing: border-box; position: relative; font-size: 20px; line-height: 1.5em; margin-top: 0; text-align: left; color: #516976;">
                                                {!! $body !!}
                                            </p>
                                        <table class="action" align="left" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; padding: 0; text-align: center; width: 100%; margin: 21px auto 0px auto;">
                                        <tbody>
                                            <tr>
                                        <td align="left" style="box-sizing: border-box; position: relative;">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; position: relative;">
                                        <tbody>
                                            <tr>
                                        <td align="left" style="box-sizing: border-box; position: relative;">
                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; position: relative;">
                                            <tbody>
                                                <tr>
                                        <td style="box-sizing: border-box; position: relative;">
                                            @foreach($actions as $action)
                                                @if(isset($action['outlined']) && $action['outlined'])
                                                    <a target="_blank" rel="noopener noreferrer" href="{{ $action['url'] }}" class="button button-primary" style="box-sizing: border-box; position: relative; -webkit-text-size-adjust: none; border-radius: 4px; color: #3865b0; display: inline-block; overflow: hidden; text-decoration: none; padding: 11px 21px; font-size: 14px; background-color: #3865b000; border: 2px solid #3865b0; font-weight: 700; text-align: center;">
                                                        {{ $action['text'] }}
                                                    </a>
                                                @else
                                                    <a target="_blank" rel="noopener noreferrer" href="{{ $action['url'] }}" class="button button-primary" style="box-sizing: border-box; position: relative; -webkit-text-size-adjust: none; border-radius: 4px; color: #fff; display: inline-block; overflow: hidden; text-decoration: none; padding: 5px 17px; font-size: 14px; background-color: #3865b0; border-bottom: 8px solid #3865b0; border-left: 18px solid #3865b0; border-right: 18px solid #3865b0; border-top: 8px solid #3865b0; text-align: center;">
                                                        {{ $action['text'] }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                </tbody>
                </table>
            </td>
            </tr>
        </tbody>
    </table>
</td>
</tr>
</tbody>
</table>



</td>
</tr>
</tbody></table>
</td>
</tr>

<tr>
<td style="box-sizing: border-box; position: relative;">
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0; text-align: center; width: 570px;">
<tbody><tr>
<td class="content-cell" align="center" style="box-sizing: border-box; position: relative; max-width: 100vw; padding: 32px 3px; text-align: left;">
<p style="box-sizing: border-box; position: relative; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: left; margin-left: -30px;">Best Invoice Solution - Send invoice fast and get paid faster
<!-- &#169; 2023 Invoice Solution. All rights reserved. -->
<br>
Click here to <a target="_blank" rel="noopener noreferrer" href="{{ config('env.spa_url') }}" style="box-sizing: border-box; position: relative; color: #b0adc5; text-decoration: underline;">create a free account</a>
<br>
<br>
Powered by <a target="_blank" rel="noopener noreferrer" href="https://softwebdigital.com" style="box-sizing: border-box; position: relative; color: #b0adc5; text-decoration: underline;">Soft-Web Digital</a>
</p>

</td>
</tr>
</tbody></table>
</td>
</tr>
</tbody></table>
</td>
</tr>
</tbody></table>

</body>
</html>