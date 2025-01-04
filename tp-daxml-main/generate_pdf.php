<?php
// Print the file parameter for debugging
if (!isset($_GET['file'])) {
    die("Error: No city file specified.");
}

$cityFile = $_GET['file'];

// Set the correct path to the XML file based on your folder structure
$xmlPath = __DIR__ . "/xml/" . basename($cityFile); // Relative path
$xslPath = __DIR__ . "/xsl/city_to_pdf.xsl"; // Path to your XSL file

// Debugging: Print the file paths
// echo "City file: " . $xmlPath . "<br>";
// echo "XSL file: " . $xslPath . "<br>";

// Check if the XML file exists
if (!file_exists($xmlPath)) {
    die("Error: City XML file not found.");
}

// Load the XML and XSL files
$xml = new DOMDocument();
$xsl = new DOMDocument();

if (!$xml->load($xmlPath)) {
    die("Error: Unable to load XML file.");
}

if (!$xsl->load($xslPath)) {
    die("Error: Unable to load XSL file.");
}

// Set up the XSLT processor
$proc = new XSLTProcessor();
$proc->importStyleSheet($xsl);

// Transform the XML into HTML
$htmlContent = $proc->transformToXML($xml);

// Include the TCPDF library
require_once('vendor/autoload.php');

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Generated PDF');
$pdf->SetSubject('PDF Generation');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// Add a page
$pdf->AddPage();

// Output the HTML content
$pdf->writeHTML($htmlContent, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('city_pdf.pdf', 'I');

exit;
?>