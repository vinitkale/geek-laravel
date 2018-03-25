@include('email.email_header')
<td valign="top" style="padding:40px 0 0;">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 13px;">
        <tbody>
            <tr style="margin: 0; padding: 0;">
                <td valign="top" style="padding: 0 26px 0 26px;"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 13px;">
                        <tbody>

                            <tr>
                                <td width="100%" valign="top" align="left" style="color: inherit; font-size: inherit; font-weight: inherit; line-height: 1; text-decoration: inherit; font-family: Arial;"><div style="margin: 0; outline: none; padding: 0; text-align: center;"> <span style="color: #4b4b4b; font-size: 30px; font-weight: inherit; line-height: 1.2; text-decoration: inherit; text-align: inherit; font-family: verdana;"><?php if (isset($email_heading)) echo $email_heading; ?></span></div></td>
                            </tr>
                        </tbody>

                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</td>
</tr>
<tr>
    <td valign="top" style="padding: 40px 0 0;">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 13px;">
            <tbody>
                <tr style="margin: 0; padding: 0;">
                    <td valign="top" style="padding: 0 26px 0 26px;">
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 13px;">
                            <tbody>
                                <tr>
                                    <td width="100%" valign="top" align="left" style="color: inherit; font-size: inherit; font-weight: inherit; line-height: 1; text-decoration: inherit; font-family: Arial;"><div style="margin: 0; outline: none; padding: 0; text-align: left;">
                                            <div style="margin: 0; outline: none; padding: 0; color: inherit; font-size: inherit; font-weight: inherit; line-height: inherit; text-decoration: inherit;"> <div style="color: #5c5c5c; font-size: 16px; font-weight: inherit; line-height: 1.7; text-decoration: inherit; font-family: verdana;"><?php echo $content; ?></div></div>
                                        </div>
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
<tr>
    <td valign="top">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 13px; padding: 0;">
            <tbody>
                <tr>
                    <td valign="top">
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 13px;">
                            <tbody>
                                <tr>
                                    <td width="100%" valign="top" align="left">

                                        <div style="margin-bottom: 20px;margin-top: 20px; outline: none; padding: 0 0 10px 0; text-align: center;"><a style="margin: 0; outline: none; padding: 10px 20px; background-color: #003A70; border: 3px solid #003A70; border-radius: 6px; color: #ffffff; font-family: verdana; font-size: 18px; display: inline-block; text-align: center; text-decoration: none; mso-hide: all;" href="<?php if (isset($link)) {
    echo 'http://2016.geekmeet.com/'.$link;
} else {
    echo base_url();
} ?>"> <span style="color:#ffffff;"> &nbsp; &nbsp;<?php if (isset($btntitle)) {
    echo $btntitle;
} else {
    echo 'Login now';
} ?> &nbsp;&nbsp; </span> </a> </div></td>

                                </tr>

                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>

@include('email.email_footer')