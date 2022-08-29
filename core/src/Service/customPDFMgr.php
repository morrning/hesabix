<?php

namespace App\Service;

class customPDFMgr extends \TCPDF
{
    //Page header
    public function Header() {
        // Set font
        $this->SetFont('vazirfd', 'B', 20);
        // Title
        $this->Cell(50, 15, 'Hesabix.ir',0, false, 'R', 0, '', 0, false, 'M', 'M');

    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('vazirfd', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}