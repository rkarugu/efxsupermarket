<!DOCTYPE html>
<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"><!--[if mso]><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch><o:AllowPNG/></o:OfficeDocumentSettings></xml><![endif]--><!--[if !mso]><!-->
    <!--<![endif]-->
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
        }

        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: inherit !important;
        }

        #MessageViewBody a {
            color: inherit;
            text-decoration: none;
        }

        p {
            line-height: inherit
        }

        .desktop_hide,
        .desktop_hide table {
            mso-hide: all;
            display: none;
            max-height: 0px;
            overflow: hidden;
        }

        .image_block img+div {
            display: none;
        }

        #converted-body .list_block ul,
        #converted-body .list_block ol,
        .body [class~="x_list_block"] ul,
        .body [class~="x_list_block"] ol,
        u+.body .list_block ul,
        u+.body .list_block ol {
            padding-left: 20px;
        }

        @media (max-width:520px) {
            .desktop_hide table.icons-inner {
                display: inline-block !important;
            }

            .icons-inner {
                text-align: center;
            }

            .icons-inner td {
                margin: 0 auto;
            }

            .mobile_hide {
                display: none;
            }

            .row-content {
                width: 100% !important;
            }

            .stack .column {
                width: 100%;
                display: block;
            }

            .mobile_hide {
                min-height: 0;
                max-height: 0;
                max-width: 0;
                overflow: hidden;
                font-size: 0px;
            }

            .desktop_hide,
            .desktop_hide table {
                display: table !important;
                max-height: none !important;
            }
        }
    </style>
</head>

<body class="body" style="background-color: #FFFFFF; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;">
<table class="nl-container" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #FFFFFF;">
    <tbody>
    <tr>
        <td>
            <table class="row row-1" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                <tbody>
                <tr>
                    <td>
                        <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 90%; margin: 0 auto;">
                            <tbody>
                            <tr>
                                <td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <table class="heading_block block-1" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                        <tr>
                                            <td class="pad">
                                                <h3 style="margin: 0; color: #1e0e4b; direction: ltr; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 24px; font-weight: 700; letter-spacing: normal; line-height: 120%; text-align: left; margin-top: 0; margin-bottom: 0; mso-line-height-alt: 28.799999999999997px;"><span class="tinyMce-placeholder" style="word-break: break-word;">END OF DAY STOCK TAKE FOR {{ $branch }} -  {{ now()->format('Y-m-d') }} <br></span></h3>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="paragraph_block block-2" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad">
                                                <div style="color:#444a5b;direction:ltr;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:16px;font-weight:400;letter-spacing:0px;line-height:120%;text-align:left;mso-line-height-alt:19.2px;">
                                                    <p style="margin: 0;">{{ $greeting }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="paragraph_block block-3" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad">
                                                <div style="color:#444a5b;direction:ltr;font-family:'Poppins', Arial, Helvetica, sans-serif;font-size:16px;font-weight:400;letter-spacing:0px;line-height:150%;text-align:left;mso-line-height-alt:24px;">
                                                    <p style="margin: 0;">Below is the summary of End of day stock take on Branch {{ $branch }} on {{ now()->format('Y-m-d H:i A') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="list_block block-4" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word; color: #101218; direction: ltr; font-family: 'Poppins', Arial, Helvetica, sans-serif; font-size: 16px; font-weight: 400; letter-spacing: 0px; line-height: 200%; text-align: left; mso-line-height-alt: 32px;">
                                        <tr>
                                            <td class="pad">
                                                <div style="margin-left:-20px">
                                                    <table class="table_block block-5" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                                        <tr>
                                                            <td class="pad">
                                                                <table style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; width: 100%; table-layout: fixed; direction: ltr; background-color: transparent; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-weight: 400; color: #000000; text-align: left; letter-spacing: 0px; word-break: break-all;" width="100%">
                                                                    <thead style="vertical-align: top; background-color: #EAEAEA; color: #505659; font-size: 14px; line-height: 120%; text-align: left;">
                                                                    <tr>
                                                                        <th width="25%"  style="font-size: 10px; padding: 10px; word-break: break-word; font-weight: 400; border-top: 1px solid #dddddd; border-right: 1px solid #dddddd; border-bottom: 1px solid #dddddd; border-left: 1px solid #dddddd;">BIN</th>
                                                                        <th width="15%"  style="font-size: 10px; padding: 10px; word-break: break-word; font-weight: 400; border-top: 1px solid #dddddd; border-right: 1px solid #dddddd; border-bottom: 1px solid #dddddd; border-left: 1px solid #dddddd;">USER</th>
                                                                        <th width="15%" style="font-size: 10px; padding: 10px; word-break: break-word; font-weight: 400; border-top: 1px solid #dddddd; border-right: 1px solid #dddddd; border-bottom: 1px solid #dddddd; border-left: 1px solid #dddddd;">TOTAL CATEGORIES<br></th>
                                                                        <th width="15%" style="font-size: 10px; padding: 10px; word-break: break-word; font-weight: 400; border-top: 1px solid #dddddd; border-right: 1px solid #dddddd; border-bottom: 1px solid #dddddd; border-left: 1px solid #dddddd;">COUNTED CATEGORIES<br></th>
                                                                        <th width="15%" style="font-size: 10px; padding: 10px; word-break: break-word; font-weight: 400; border-top: 1px solid #dddddd; border-right: 1px solid #dddddd; border-bottom: 1px solid #dddddd; border-left: 1px solid #dddddd;">CURRENT VARIATION<br></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody style="vertical-align: top; font-size: 14px; line-height: 120%;">
                                                                    @foreach ($shift_data as $checkName => $checkDetails)

                                                                        <tr>
                                                                            @foreach($checkDetails as $key => $val)
                                                                                <td style="font-size: 12px; padding: 10px; word-break: break-word; border-top: 1px solid #dddddd; border-right: 1px solid #dddddd; border-bottom: 1px solid #dddddd; border-left: 1px solid #dddddd;">{{ $val }}</td>
                                                                            @endforeach
                                                                        </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="button_block block-6" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                        <tr>
                                            <td class="pad">
                                                <div class="alignment" align="center"><!--[if mso]>
                                                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://localhost:8000/admin/sales-and-receivables-reports-load" style="height:42px;width:168px;v-text-anchor:middle;" arcsize="10%" stroke="false" fillcolor="#211838">
                                                        <w:anchorlock/>
                                                        <v:textbox inset="0px,0px,0px,0px">
                                                            <center dir="false" style="color:#ffffff;font-family:Arial, sans-serif;font-size:16px">
                                                    <![endif]--><a href="{{ route('operation_shifts.show', $shift->id) }}" target="_blank" style="background-color:#211838;border-bottom:0px solid transparent;border-left:0px solid transparent;border-radius:4px;border-right:0px solid transparent;border-top:0px solid transparent;color:#ffffff;display:inline-block;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:16px;font-weight:400;mso-border-alt:none;padding-bottom:5px;padding-top:5px;text-align:center;text-decoration:none;width:auto;word-break:keep-all;"><span style="word-break: break-word; padding-left: 20px; padding-right: 20px; font-size: 16px; display: inline-block; letter-spacing: normal;"><span style="word-break: break-word; line-height: 32px;">View More Details</span></span></a><!--[if mso]></center></v:textbox></v:roundrect><![endif]--></div>
                                            </td>
                                        </tr>
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
    </tbody>
</table><!-- End -->
</body>

</html>