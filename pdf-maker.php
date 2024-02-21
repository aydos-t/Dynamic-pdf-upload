<?php
require 'connect.php';
include_once('tcpdf_6_2_13/tcpdf/tcpdf.php');

$MST_ID = $_GET['MST_ID'];

$inv_mst_query = "SELECT T1.MST_ID, T1.INV_NO, T1.CUSTOMER_NAME, T1.CUSTOMER_MOBILENO, T1.ADDRESS FROM INVOICE_MST T1 WHERE T1.MST_ID='" . $MST_ID . "' ";
$inv_mst_results = mysqli_query($connect, $inv_mst_query);
$count = mysqli_num_rows($inv_mst_results);
if ($count > 0) {
    $inv_mst_data_row = mysqli_fetch_array($inv_mst_results, MYSQLI_ASSOC);

    //----- Код для генерации pdf
    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf -> SetCreator(PDF_CREATOR);
    //$pdf->SetTitle("Экспорт данных HTML-таблицы в PDF с помощью TCPDF в PHP");
    $pdf -> SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
    $pdf -> setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf -> setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf -> SetDefaultMonospacedFont('helvetica');
    $pdf -> SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf -> SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);
    $pdf -> setPrintHeader(false);
    $pdf -> setPrintFooter(false);
    $pdf -> SetAutoPageBreak(TRUE, 10);
    $pdf -> SetFont('helvetica', '', 12);
    $pdf -> AddPage(); //Поумолчанию A4
    //$pdf->AddPage('P','A5'); //когда вам нужен индивидуальный размер страницы

    $content = '';

    $content = '
	<style type="text/css">
	body{
	font-size:12px;
	line-height:24px;
	font-family:"Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
	color:#000;
	}
	</style>    
	<table cellpadding="0" cellspacing="0" style="border:1px solid #ddc;width:100%;">
	<table style="width:100%;" >
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2" align="center"><b>Google</b></td></tr>
	<tr><td colspan="2" align="center"><b>CONTACT: +998 90 652-10-30</b></td></tr>
	<tr><td colspan="2" align="center"><b>WEBSITE: WWW.GOOLE.COM</b></td></tr>
	<tr><td colspan="2"><b>CUSTOMER NAME: ' . $inv_mst_data_row['CUSTOMER_NAME'] . ' </b></td></tr>
	<tr><td><b>MOBILE NUMBER: ' . $inv_mst_data_row['CUSTOMER_MOBILENO'] . ' </b></td><td align="right"><b>BILL DT.: ' . date("d-m-Y") . '</b> </td></tr>
	<tr><td>&nbsp;</td><td align="right"><b>BILL NO.: ' . $inv_mst_data_row['INV_NO'] . '</b></td></tr>
	<tr><td colspan="2" align="center"><b>Invoice</b></td></tr>
	<tr class="heading" style="background:#eee;border-bottom:1px solid #ddd;font-weight:bold;">
		<td>
			TYPE OF WORK
		</td>
		<td align="right">
			AMOUNT
		</td>
	</tr>';
    $total = 0;
    $inv_det_query = "SELECT T2.PRODUCT_NAME, T2.AMOUNT FROM INVOICE_DET T2 WHERE T2.MST_ID='" . $MST_ID . "' ";
    $inv_det_results = mysqli_query($connect, $inv_det_query);
    while ($inv_det_data_row = mysqli_fetch_array($inv_det_results, MYSQLI_ASSOC)) {
        $content .= '
		  <tr class="itemrows">
			  <td>
				  <b>' . $inv_det_data_row['PRODUCT_NAME'] . '</b>
				  <br>
				  <i>Write any remarks</i>
			  </td>
			  <td align="right"><b>
				  ' . $inv_det_data_row['AMOUNT'] . '
			  </b></td>
		  </tr>';
        $total = $total + $inv_det_data_row['AMOUNT'];
    }
    $content .= '<tr class="total"><td colspan="2" align="right">------------------------</td></tr>
		<tr><td colspan="2" align="right"><b>GRAND&nbsp;TOTAL:&nbsp;' . $total . '</b></td></tr>
		<tr><td colspan="2" align="right">------------------------</td></tr>
	<tr><td colspan="2" align="right"><b>PAYMENT MODE: CASH/ONLINE </b></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2" align="center"><b>THANK YOU ! VISIT AGAIN</b></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	</table>
</table>';
    $pdf -> writeHTML($content);

    $file_location = "e:/OSPanel/domains/TZ/TBG/uploads/"; //добавьте полный путь к вашему серверу

    $datetime = date('dmY_hms');
    $file_name = "INV_" . $datetime . ".pdf";
    ob_end_clean();

    if ($_GET['ACTION'] == 'VIEW') {
        $pdf -> Output($file_name, 'I');
    } else if ($_GET['ACTION'] == 'DOWNLOAD') {
        $pdf -> Output($file_name, 'D'); // D означает скачать
    } else if ($_GET['ACTION'] == 'UPLOAD') {
        $pdf -> Output($file_location . $file_name, 'F'); // F означает загрузку PDF-файла в некоторую папку
        echo "Upload successfully!!" . '<br/><br/> <a href="index.php">Назад в Главную страницу</a>';
    } else if ($_GET['ACTION'] == 'EMAIL') {
        $pdf -> Output($file_location . $file_name, 'F'); // F означает загрузку PDF-файла в некоторую папку
//echo "Электронная почта отправлена успешно!!";
        error_reporting(E_ALL ^ E_DEPRECATED);
        include_once('PHPMailer/phpMailer.php');
        require('PHPMailer/phpMailerAutoload.php');

        $body = '';
        $body .= "<html>
	<head>
	<style type='text/css'> 
	body {
	font-family: Calibri;
	font-size:16px;
	color:#000;
	}
	</style>
	</head>
	<body>
	Dear customer,
	<br>
	Please find attached invoice copy.
	<br>
	Thank you!
	</body>
	</html>";

        $mail = new PHPMailer();
        $mail -> CharSet = 'UTF-8';
        $mail -> IsMAIL();
        $mail -> IsSMTP();
        $mail -> Subject = "Invoice details";
        $mail -> From = "a.tazhiniazov@gmail.com";
        $mail -> FromName = "Aydos-T";
        $mail -> IsHTML(true);
        $mail -> AddAddress('tazhiniazov.a@gmail.com'); // To mail id

        $mail -> AddAttachment($file_location . $file_name);
        $mail -> MsgHTML($body);
        $mail -> WordWrap = 50;
        $mail -> Send();
        $mail -> SmtpClose();
        if ($mail -> IsError()) {
            echo "Mailer Error: " . $mail -> ErrorInfo;
        } else {
            echo "Message sent!";
        };
    }
//----- Конец кода для генерации pdf

} else {
    echo 'Запись не найдена для PDF..';
}
?>