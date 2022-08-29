<?php

namespace App\Service;
use Twig\Environment;
class pdfMGR
{

    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function streamTwig2PDF($twig_file,$params){
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Hesabix by sarkesh LTD');
        $pdf->SetTitle('Hesabix');
        $pdf->SetSubject('Hesabix software');

// set default header data
        $pdf->SetHeaderData(null, null, 'Hesabix.ir', 'نرم‌افزار حسابداری آنلاین حسابیکس');

// set header and footer fonts
        $pdf->setHeaderFont(Array('vazirfd', '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array('vazirfd', '', PDF_FONT_SIZE_DATA));

// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

// set font
        $pdf->SetFont('vazirfd', '', 10);
        $pdf->setRTL(true);
        $pdf->addTOCPage('L');

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// create some HTML content
        $html = $this->twig->render($twig_file,$params);

// output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('I');
    }
}