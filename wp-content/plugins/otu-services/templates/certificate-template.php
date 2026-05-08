<?php
/**
 * TCPDF Certificate Template for OTU Services.
 *
 * This file is included by OTU_Certificate::generate_pdf() and assumes
 * a TCPDF instance ($pdf) has already been created and a page added.
 * It renders the maroon/gold branded certificate layout.
 *
 * Variables expected to be in scope when this template is included:
 *   @var TCPDF  $pdf            Configured TCPDF instance.
 *   @var string $student_name   Full student name.
 *   @var string $course_name    Full course name.
 *   @var string $certificate_id Unique certificate ID string (e.g. OTU-ABCD1234).
 *   @var string $issue_date     Human-readable issue date (e.g. June 1, 2025).
 *   @var string $site_name      Organisation name for footer.
 *   @var string $site_url       Organisation URL for footer.
 *
 * @package OTU_Services
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Colour definitions ─────────────────────────────────────
//   Maroon : r=128, g=0,   b=32
//   Gold   : r=212, g=175, b=55
//   Dark   : r=44,  g=0,   b=0
// ───────────────────────────────────────────────────────────

// ── Page layout: A4 Landscape (297mm × 210mm) ───────────────
// Margins are already set in generate_pdf(). We just draw content.

// ── 1. Maroon header band ───────────────────────────────────
$pdf->SetFillColor( 128, 0, 32 );
$pdf->Rect( 0, 0, 297, 35, 'F' );

// ── 2. Gold accent stripe under header ─────────────────────
$pdf->SetFillColor( 212, 175, 55 );
$pdf->Rect( 0, 35, 297, 4, 'F' );

// ── 3. Organisation name (gold on maroon) ──────────────────
$pdf->SetFont( 'helvetica', 'B', 22 );
$pdf->SetTextColor( 212, 175, 55 );
$pdf->SetXY( 0, 7 );
$pdf->Cell( 297, 12, strtoupper( $site_name ), 0, 1, 'C' );

// ── 4. Website URL ──────────────────────────────────────────
$pdf->SetFont( 'helvetica', '', 11 );
$pdf->SetTextColor( 255, 255, 255 );
$pdf->SetXY( 0, 21 );
$pdf->Cell( 297, 8, $site_url, 0, 1, 'C' );

// ── 5. "Certificate of Completion" title ───────────────────
$pdf->SetFont( 'times', 'B', 28 );
$pdf->SetTextColor( 128, 0, 32 );
$pdf->SetXY( 0, 52 );
$pdf->Cell( 297, 16, 'Certificate of Completion', 0, 1, 'C' );

// ── 6. Top decorative rule (gold) ──────────────────────────
$pdf->SetDrawColor( 212, 175, 55 );
$pdf->SetLineWidth( 0.7 );
$pdf->Line( 50, 71, 247, 71 );

// ── 7. "This certifies that" ───────────────────────────────
$pdf->SetFont( 'times', 'I', 13 );
$pdf->SetTextColor( 80, 80, 80 );
$pdf->SetXY( 0, 75 );
$pdf->Cell( 297, 10, 'This certifies that', 0, 1, 'C' );

// ── 8. Student name (large, serif italic, maroon) ──────────
$pdf->SetFont( 'times', 'BI', 34 );
$pdf->SetTextColor( 128, 0, 32 );
$pdf->SetXY( 0, 85 );
$pdf->Cell( 297, 18, $student_name, 0, 1, 'C' );

// ── 9. "has successfully completed" ────────────────────────
$pdf->SetFont( 'times', 'I', 13 );
$pdf->SetTextColor( 80, 80, 80 );
$pdf->SetXY( 0, 105 );
$pdf->Cell( 297, 10, 'has successfully completed', 0, 1, 'C' );

// ── 10. Course name (bold, dark) ───────────────────────────
$pdf->SetFont( 'times', 'B', 20 );
$pdf->SetTextColor( 44, 0, 0 );
$pdf->SetXY( 0, 116 );
$pdf->Cell( 297, 14, $course_name, 0, 1, 'C' );

// ── 11. Middle decorative rule ─────────────────────────────
$pdf->Line( 50, 133, 247, 133 );

// ── 12. Date & Certificate ID row ──────────────────────────
$pdf->SetFont( 'helvetica', '', 10 );
$pdf->SetTextColor( 90, 90, 90 );

$pdf->SetXY( 50, 140 );
$pdf->Cell( 90, 7, 'Date of Completion', 0, 0, 'C' );
$pdf->SetXY( 157, 140 );
$pdf->Cell( 90, 7, 'Certificate ID', 0, 1, 'C' );

$pdf->SetFont( 'helvetica', 'B', 12 );
$pdf->SetTextColor( 128, 0, 32 );

$pdf->SetXY( 50, 147 );
$pdf->Cell( 90, 8, $issue_date, 0, 0, 'C' );
$pdf->SetXY( 157, 147 );
$pdf->Cell( 90, 8, $certificate_id, 0, 1, 'C' );

// ── 13. Signature line ─────────────────────────────────────
$pdf->SetDrawColor( 100, 100, 100 );
$pdf->SetLineWidth( 0.3 );
$pdf->Line( 85, 164, 212, 164 );

$pdf->SetFont( 'helvetica', 'I', 10 );
$pdf->SetTextColor( 100, 100, 100 );
$pdf->SetXY( 0, 166 );
$pdf->Cell( 297, 7, 'Authorised Signature — ' . $site_name, 0, 1, 'C' );

// ── 14. Decorative bottom band ─────────────────────────────
$pdf->SetFillColor( 212, 175, 55 );
$pdf->Rect( 0, 182, 297, 4, 'F' );

$pdf->SetFillColor( 128, 0, 32 );
$pdf->Rect( 0, 186, 297, 24, 'F' );

// ── 15. Footer text ────────────────────────────────────────
$pdf->SetFont( 'helvetica', '', 9 );
$pdf->SetTextColor( 212, 175, 55 );
$pdf->SetXY( 0, 192 );
$pdf->Cell( 297, 8, $site_url . '  ·  ' . 'Certificate ID: ' . $certificate_id, 0, 1, 'C' );
