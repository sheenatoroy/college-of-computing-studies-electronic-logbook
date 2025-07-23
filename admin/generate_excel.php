<?php
session_start();
include "../connection.php";
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

// Ensure no output before this point
ob_start();

// Retrieve and validate filter criteria from POST
$faculty = $_POST['faculty'];
$fromMonth = $_POST['fromMonth'];
$toMonth = $_POST['toMonth'];
$year = $_POST['year'];

// Debugging: Output the received values
error_log("Faculty: $faculty");
error_log("From Month: $fromMonth");
error_log("To Month: $toMonth");
error_log("Year: $year");

// Check if "Select Faculty" is chosen
if ($faculty == "Select Faculty") {
    // Build query to retrieve all appointments without filtering by date or faculty
    $sql = "SELECT
                a.appointment_id,
                CONCAT(s.firstname, ' ', s.lastname) AS student_name,
                s.year_section AS student_year_section,
                a.time_start,
                a.time_end,
                a.day,
                a.type_of_concern,
                a.specific_concern,
                a.detailed_concern,
                a.remarks,
                a.evaluation_status,
                a.action_report,
                a.resched_reason,
                a.appoint_by,
                a.app_day,
                a.action_report_path,
                a.action_report_textbox,
                a.services_rendered,
                a.total_hours,
                CONCAT(p.firstname, ' ', p.lastname) AS professor_name
            FROM appointments a
            JOIN student s ON a.student_id = s.username
            JOIN prof p ON a.prof_id = p.username
            WHERE a.remarks IN ('done', 'unresolved')
            ORDER BY a.day ASC";
} else {
    // Check if required inputs are provided
    if (empty($fromMonth) || empty($toMonth) || empty($year)) {
        echo '<script>alert("Please select in all required selection fields: From Month, To Month, Year."); history.back();</script>';
        exit;
    }

    // Convert fromMonth and toMonth to date range
    $fromDate = "$year-$fromMonth-01";
    $toDate = date("Y-m-t", strtotime("$year-$toMonth-01")); // Get the last day of the toMonth

    // Retrieve appointments for the current professor within the specified date range
    $sql = "SELECT
                a.appointment_id,
                CONCAT(s.firstname, ' ', s.lastname) AS student_name,
                s.year_section AS student_year_section,
                a.time_start,
                a.time_end,
                a.day,
                a.type_of_concern,
                a.specific_concern,
                a.detailed_concern,
                a.remarks,
                a.evaluation_status,
                a.action_report,
                a.resched_reason,
                a.appoint_by,
                a.app_day,
                a.action_report_path,
                a.action_report_textbox,
                a.services_rendered,
                a.total_hours,
                CONCAT(p.firstname, ' ', p.lastname) AS professor_name
            FROM appointments a
            JOIN student s ON a.student_id = s.username
            JOIN prof p ON a.prof_id = p.username
            WHERE p.username = '$faculty'
            AND a.remarks IN ('done', 'unresolved')
            AND a.app_day BETWEEN '$fromDate' AND '$toDate'
            ORDER BY a.day ASC";
}

$result = $conn->query($sql);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set default font to Century Gothic
$spreadsheet->getDefaultStyle()->getFont()->setName('Century Gothic');

// Set page setup settings
$sheet->getPageSetup()
    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
    ->setPaperSize(PageSetup::PAPERSIZE_A3)
    ->setScale(75);

$sheet->getPageMargins()->setTop(0.8);
$sheet->getPageMargins()->setHeader(0.6);
$sheet->getPageMargins()->setRight(0.9);
$sheet->getPageMargins()->setLeft(0.9);
$sheet->getPageMargins()->setBottom(0.5);
$sheet->getPageMargins()->setFooter(0.4);

$sheet->getHeaderFooter()->setScaleWithDocument(true);

// Add headers
$sheet->setCellValue('I1', 'PNC:AA-FO-17 rev.0 03202023');
$sheet->getStyle('I1')->applyFromArray([
    'font' => [
        'name' => 'Century Gothic',
        'size' => 9,
    ],
]);

$sheet->getStyle('I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


$sheet->mergeCells('A2:I2');
$sheet->setCellValue('A2', 'Republic of the Philippines');
$sheet->getStyle('A2')->applyFromArray([
    'font' => [
        'name' => 'Times New Roman',
        'size' => 12,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);

$sheet->mergeCells('A3:I3');
$sheet->setCellValue('A3', 'Pamantasan ng Cabuyao');
$sheet->getStyle('A3')->applyFromArray([
    'font' => [
        'name' => 'Old English Text MT',
        'size' => 20,
        'bold' => true,
        'color' => ['rgb' => '003300'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);
$sheet->getStyle('A3')->getAlignment()->setWrapText(false);

$sheet->mergeCells('A4:I4');
$sheet->setCellValue('A4', '(University of Cabuyao)');
$sheet->getStyle('A4')->applyFromArray([
    'font' => [
        'name' => 'Copperplate Gothic Bold',
        'size' => 15,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);

$sheet->mergeCells('A5:I5');
$sheet->setCellValue('A5', 'Academic Affairs Division');
$sheet->getStyle('A5')->applyFromArray([
    'font' => [
        'name' => 'Calibri',
        'size' => 12,
        'bold' => true,
        'italic' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);

$sheet->mergeCells('A6:I6');
$sheet->setCellValue('A6', 'Katapatan Mutual Homes, Brgy. Banay-banay, City of Cabuyao, Laguna 4025');
$sheet->getStyle('A6')->applyFromArray([
    'font' => [
        'name' => 'Times New Roman',
        'size' => 9,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);

$sheet->mergeCells('A9:I9');
$sheet->setCellValue('A9', 'ACADEMIC CONSULTATION AND CLASS/YEAR LEVEL ADVISING FORM');
$sheet->getStyle('A9')->applyFromArray([
    'font' => [
        'name' => 'Century Gothic',
        'size' => 12,
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_BOTTOM,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'C0C0C0'],
    ],
]);

$sheet->mergeCells('A10:I10');
$sheet->setCellValue('A10', 'College/Department: Computing Studies');
$sheet->getStyle('A10')->applyFromArray([
    'font' => [
        'name' => 'Century Gothic',
        'size' => 11,
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_BOTTOM,
    ],
]);

$sheet->mergeCells('A11:I11');
$sheet->setCellValue('A11', '1st Semester, Academic Year 2023-2024');
$sheet->getStyle('A11')->applyFromArray([
    'font' => [
        'name' => 'Century Gothic',
        'size' => 11,
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_BOTTOM,
    ],
]);




// Add "PROGRAM/STRAND" to A12
$sheet->setCellValue('A12', 'PROGRAM/STRAND');
$sheet->setCellValue('B12', 'Bachelor of Science in Information Technology');
$sheet->getStyle('A12')->getFont()->setBold(true);
$sheet->getStyle('B12')->getFont()->setBold(true);
$sheet->getStyle('A14:I15')->getFont()->setBold(true);
$sheet->mergeCells('B12:C12');



// Add logo image
$drawing = new Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath('uc_logo.png'); // Path to your logo file
$drawing->setHeight(110);
$drawing->setWidth(110);
$drawing->setCoordinates('C1');
$drawing->setWorksheet($sheet);


// Add header at row 15
$sheet->mergeCells('A14:A15'); //merge A14 and A15
$sheet->mergeCells('B14:B15');
$sheet->mergeCells('C14:C15');

$sheet->mergeCells('D14:E14');
$sheet->mergeCells('F14:F15');
$sheet->mergeCells('G14:G15');
$sheet->mergeCells('H14:H15');
$sheet->mergeCells('I14:I15');
//$sheet->mergeCells('J14:J15');


$sheet->setCellValue('A14', 'NAME OF STUDENT');
$sheet->setCellValue('B14', 'Grade/Year/ Course & Section');
$sheet->setCellValue('C14', 'Date Conducted (m/d/y)');
$sheet->setCellValue('D14', 'TIME CONDUCTED');
$sheet->setCellValue('D15', 'TIME START');
$sheet->setCellValue('E15', 'TIME END');
$sheet->setCellValue('F14', '*Services Rendered (No. 1-6)');
$sheet->setCellValue('G14', 'Concern/s');
$sheet->setCellValue('H14', 'Action/s Taken');
$sheet->setCellValue('I14', 'No. of Hrs Rendered');
//$sheet->setCellValue('J14', 'NAME OF PROF');
$sheet->getStyle('A14:I14')->getFont()->setName('Century Gothic');
$sheet->getStyle('A14:I14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A14:I14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('C0C0C0'); // Lavender 40% - Accent 4
$sheet->getStyle('D15:E15')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('C0C0C0'); // Lavender 40% - Accent 4

// Apply border to header row
$sheet->getStyle('A14:I15')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


// Add data from database starting from row 16
$row = 16;
if ($result->num_rows > 0) {
    while ($data = $result->fetch_assoc()) {
        

        // Use correct field names based on your database schema
        $sheet->setCellValue('A' . $row, $data['student_name']);
        $sheet->setCellValue('B' . $row, $data['student_year_section']);
        $sheet->setCellValue('C' . $row, $formattedDate);
        $sheet->setCellValue('D' . $row, $time_start);
        $sheet->setCellValue('E' . $row, $time_end);
        $sheet->setCellValue('F' . $row, $data['services_rendered']);
        $sheet->setCellValue('G' . $row, $data['detailed_concern']);
        $sheet->setCellValue('H' . $row, $data['action_report_textbox']);
        $sheet->setCellValue('I' . $row, $data['total_hours']);
        //$sheet->setCellValue('J' . $row, $data['professor_name']);

        // Apply border to each data row
        $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $row++;
    }
}

// Set wrap text for all columns
foreach (range('A', 'I') as $columnID) {
    $sheet->getStyle($columnID . '1:' . $columnID . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
}

// Set auto-size for all columns except B and H
foreach (range('A', 'I') as $columnID) {
    if ($columnID !== 'B' && $columnID !== 'H') {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
}



//TOTAL HOURS
// Find the first empty cell after H16
$emptyCellH = 'H16';
while ($sheet->getCell($emptyCellH)->getValue() != "") {
    $emptyCellH++;
}
// Insert "Total Hours" in the empty cell after H27, make it bold and italicized
$sheet->setCellValue($emptyCellH, 'Total Hours');
$sheet->getStyle($emptyCellH)->applyFromArray([
    'font' => [
        'bold' => true,
        'italic' => true,
        'name' => 'Century Gothic',
    ]
]);

//SUM OF HOURS
// Find the first empty cell after I27
$emptyCellI = 'I16';
while ($sheet->getCell($emptyCellI)->getValue() != "") {
    $emptyCellI++;
}
// Insert sum of all values in column I starting from I28 in the empty cell after I27
$sheet->setCellValue($emptyCellI, "=SUM(I16:I" . ($row - 1) . ")");
$sheet->getStyle($emptyCellI)->applyFromArray([
    'font' => [
        'name' => 'Century Gothic',
    ]
]);

//SERVICES RENDERED
// Find the first empty cell after 'A16'
$emptyCellA = 'A16';
while ($sheet->getCell($emptyCellA)->getValue() != "") {
    $emptyCellA++;
}

$emptyCellA++; // Mag-leave ng isang cell
$sheet->setCellValue($emptyCellA, '*Services Rendered');
$sheet->getStyle($emptyCellA)->applyFromArray([
    'font' => [
        'bold' => true,
        'italic' => true,
        'name' => 'Century Gothic',
    ]
]);



//ACADEMIC CONSULTATION
// Find the next empty cell after '*Services Rendered'
$emptyCellA++;
while ($sheet->getCell($emptyCellA)->getValue() != "") {
    $emptyCellA++;
}

$sheet->setCellValue($emptyCellA, 'Academic Consultation');
$sheet->getStyle($emptyCellA)->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 9,
        'name' => 'Century Gothic',
    ]
]);


//1. Improvement of Learning Strategies:
// Find the next empty cell after '*Academic Consultation'
$emptyCellA++;
while ($sheet->getCell($emptyCellA)->getValue() != "") {
    $emptyCellA++;
}

$sheet->setCellValue($emptyCellA, '(1) Improvement of Learning Strategies:');
$sheet->getStyle($emptyCellA)->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

//2. Record-Keeping:
// Find the next empty cell after '1. Improvement of Learning Strategies:'
$emptyCellA++;
while ($sheet->getCell($emptyCellA)->getValue() != "") {
    $emptyCellA++;
}

$sheet->setCellValue($emptyCellA, '(2) Record-Keeping:');
$sheet->getStyle($emptyCellA)->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

//3. Tracking of Low-Achieving Students
// Find the next empty cell after '2. Record-Keeping:'
$emptyCellA++;
while ($sheet->getCell($emptyCellA)->getValue() != "") {
    $emptyCellA++;
}

$sheet->setCellValue($emptyCellA, '(3) Tracking of Low-Achieving Students ');
$sheet->getStyle($emptyCellA)->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

//(4) Decision Making: 
// Find the next empty cell after '3. Tracking of Low-Achieving Students'
$emptyCellA++;
while ($sheet->getCell($emptyCellA)->getValue() != "") {
    $emptyCellA++;
}

$sheet->setCellValue($emptyCellA, '(4) Decision Making: ');
$sheet->getStyle($emptyCellA)->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

//(5) Information: 
// Find the next empty cell after '(4) Decision Making: '
$emptyCellA++;
while ($sheet->getCell($emptyCellA)->getValue() != "") {
    $emptyCellA++;
}

$sheet->setCellValue($emptyCellA, '(5) Information: ');
$sheet->getStyle($emptyCellA)->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

//(6) Referral: 
// Find the next empty cell after '(5) Information: '
$emptyCellA++;
while ($sheet->getCell($emptyCellA)->getValue() != "") {
    $emptyCellA++;
}

$sheet->setCellValue($emptyCellA, '(6) Referral: ');
$sheet->getStyle($emptyCellA)->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

//(7) Others: 
// Find the next empty cell after '(6) Referral:'
$emptyCellA++;
while ($sheet->getCell($emptyCellA)->getValue() != "") {
    $emptyCellA++;
}

$sheet->setCellValue($emptyCellA, '(7) Others:');
$sheet->getStyle($emptyCellA)->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);


//PREPARED BY
// Find the next empty cell after '(7) Others:'
$emptyCellA++;
while ($sheet->getCell($emptyCellA)->getValue() != "") {
    $emptyCellA++;
}

$emptyCellA++;
$sheet->setCellValue($emptyCellA, 'Prepared by:');
$sheet->getStyle($emptyCellA)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 10,
        'name' => 'Century Gothic',
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
    ]
]);


//SUBMITTED ON
// Find the next empty cell after '(7) Others:'
$emptyCellA++;
while ($sheet->getCell($emptyCellA)->getValue() != "") {
    $emptyCellA++;
}

$emptyCellA++;
$emptyCellA++;
$sheet->setCellValue($emptyCellA, 'Submitted on:');
$sheet->getStyle($emptyCellA)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 10,
        'name' => 'Century Gothic',
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
    ]
]);


//________________________________________B________________________
// Find the first empty cell after 'B16'
$emptyCellB = 'B16';
while ($sheet->getCell($emptyCellB)->getValue() != "") {
    $emptyCellB++;
}

// first
$emptyCellB++; // Mag-leave ng isang cell
$emptyCellB++; // Mag-leave ng isang cell
$emptyCellB++; // Mag-leave ng isang cell
$servicesRenderedCell = $emptyCellB;
$sheet->setCellValue($servicesRenderedCell, 'Offers guidance on effective study strategies to enhance learning; suggests ways to improve class participation; conducts tutorials, review sessions, or project consultations.');
$sheet->getStyle($servicesRenderedCell)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);


// second
$emptyCellB++;
while ($sheet->getCell($emptyCellB)->getValue() != "") {
    $emptyCellB++;
}

$sheet->setCellValue($emptyCellB, 'Collaborates with the Guidance and Counseling Department or relevant teaching personnel in gathering pertinent information about students to be used in individual academic conferences and policy-making.');
$sheet->getStyle($emptyCellB)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

// third
$emptyCellB++;
while ($sheet->getCell($emptyCellB)->getValue() != "") {
    $emptyCellB++;
}

$sheet->setCellValue($emptyCellB, 'Provides instructional support to struggling students, such as those who received 75% and below grades in their term.');
$sheet->getStyle($emptyCellB)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

// fourth
$emptyCellB++;
while ($sheet->getCell($emptyCellB)->getValue() != "") {
    $emptyCellB++;
}

$sheet->setCellValue($emptyCellB, 'Provides assistance in defining goals and objectives, understanding available choices and assessing the consequences of alternative courses of action.');
$sheet->getStyle($emptyCellB)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

// fifth
$emptyCellB++;
while ($sheet->getCell($emptyCellB)->getValue() != "") {
    $emptyCellB++;
}

$sheet->setCellValue($emptyCellB, 'Provides clear and accurate information regarding institutional policies, procedures, resources and programs.');
$sheet->getStyle($emptyCellB)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

// sixth
$emptyCellB++;
while ($sheet->getCell($emptyCellB)->getValue() != "") {
    $emptyCellB++;
}

$sheet->setCellValue($emptyCellB, 'Provides referral to appropriate institutional or community support services as may be needed.');
$sheet->getStyle($emptyCellB)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

// seventh
$emptyCellB++;
while ($sheet->getCell($emptyCellB)->getValue() != "") {
    $emptyCellB++;
}

$sheet->setCellValue($emptyCellB, 'Kindly specify _____________________');
$sheet->getStyle($emptyCellB)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);


//AFTER NG LAHAT
// Find the next empty cell after 'Provides instructional'
$emptyCellB++;
while ($sheet->getCell($emptyCellB)->getValue() != "") {
    $emptyCellB++;
}

$emptyCellB++;
// Insert "Kindly specify" sa susunod na walang laman na cell pagkatapos ng '*Services Rendered', at gawing bold, at ang font size ay 9
$sheet->setCellValue($emptyCellB, '_________________');
$sheet->getStyle($emptyCellB)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 9,
        'name' => 'Century Gothic',
    ]
]);

// Find the next empty cell after 'Provides instructional'
$emptyCellB++;
while ($sheet->getCell($emptyCellB)->getValue() != "") {
    $emptyCellB++;
}

// Insert "Kindly specify" sa susunod na walang laman na cell pagkatapos ng '*Services Rendered', at gawing bold, at ang font size ay 9
$sheet->setCellValue($emptyCellB, 'Signature over printed name');
$sheet->getStyle($emptyCellB)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

// Find the next empty cell after 'Provides instructional'
$emptyCellB++;
while ($sheet->getCell($emptyCellB)->getValue() != "") {
    $emptyCellB++;
}

// Insert "Kindly specify" sa susunod na walang laman na cell pagkatapos ng '*Services Rendered', at gawing bold, at ang font size ay 9
$sheet->setCellValue($emptyCellB, 'Teaching Personnel');
$sheet->getStyle($emptyCellB)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 10,
        'name' => 'Century Gothic',
    ]
]);

// Find the next empty cell after 'Provides instructional'
$emptyCellB++;
while ($sheet->getCell($emptyCellB)->getValue() != "") {
    $emptyCellB++;
}

// Insert "Kindly specify" sa susunod na walang laman na cell pagkatapos ng '*Services Rendered', at gawing bold, at ang font size ay 9
$sheet->setCellValue($emptyCellB, '_________________');
$sheet->getStyle($emptyCellB)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 10,
        'name' => 'Century Gothic',
    ]
]);


//_____________________________E____________
//REVIEWED BY
// Find the first empty cell after 'E16'
$emptyCellE = 'E16';
while ($sheet->getCell($emptyCellE)->getValue() != "") {
    $emptyCellE++;
}

$emptyCellE++; // Mag-leave ng isang cell
$emptyCellE++; // Mag-leave ng isang cell
$emptyCellE++; // Mag-leave ng isang cell
$emptyCellE++; // Mag-leave ng isang cell
$emptyCellE++; // Mag-leave ng isang cell
$emptyCellE++; // Mag-leave ng isang cell
$emptyCellE++; // Mag-leave ng isang cell
$emptyCellE++; // Mag-leave ng isang cell
$emptyCellE++; // Mag-leave ng isang cell
$emptyCellE++; // Mag-leave ng isang cell
$emptyCellE++; // Mag-leave ng isang cell
$sheet->setCellValue($emptyCellE, 'Reviewed by: ');
$sheet->getStyle($emptyCellE)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 10,
        'name' => 'Century Gothic',
    ]
]);

//DATE
// Find the next empty cell after 'REVIEWED BY
$emptyCellE++;
while ($sheet->getCell($emptyCellE)->getValue() != "") {
    $emptyCellE++;
}

$emptyCellE++;
$emptyCellE++;
$sheet->setCellValue($emptyCellE, 'Date: ');
$sheet->getStyle($emptyCellE)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);


//_____________________________F____________
//BESIDE REVIEWED BY
// Find the first empty cell after 'F16'
$emptyCellF = 'F16';
while ($sheet->getCell($emptyCellF)->getValue() != "") {
    $emptyCellF++;
}

$emptyCellF++; // Mag-leave ng isang cell
$emptyCellF++; // Mag-leave ng isang cell
$emptyCellF++; // Mag-leave ng isang cell
$emptyCellF++; // Mag-leave ng isang cell
$emptyCellF++; // Mag-leave ng isang cell
$emptyCellF++; // Mag-leave ng isang cell
$emptyCellF++; // Mag-leave ng isang cell
$emptyCellF++; // Mag-leave ng isang cell
$emptyCellF++; // Mag-leave ng isang cell
$emptyCellF++; // Mag-leave ng isang cell
$emptyCellF++; // Mag-leave ng isang cell
$sheet->setCellValue($emptyCellF, '____________________');
$sheet->getStyle($emptyCellF)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 10,
        'name' => 'Century Gothic',
    ]
]);

//signature
// Find the next empty cell after '____________
$emptyCellF++;
while ($sheet->getCell($emptyCellF)->getValue() != "") {
    $emptyCellF++;
}

$sheet->setCellValue($emptyCellF, 'Signature over printed name');
$sheet->getStyle($emptyCellF)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

//position
// Find the next empty cell after 'signature
$emptyCellF++;
while ($sheet->getCell($emptyCellF)->getValue() != "") {
    $emptyCellF++;
}

$sheet->setCellValue($emptyCellF, 'Department Chair/Coordinator');
$sheet->getStyle($emptyCellF)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 10,
        'name' => 'Century Gothic',
    ]
]);

//______
// Find the next empty cell after 'position
$emptyCellF++;
while ($sheet->getCell($emptyCellF)->getValue() != "") {
    $emptyCellF++;
}

$sheet->setCellValue($emptyCellF, '____________________');
$sheet->getStyle($emptyCellF)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 10,
        'name' => 'Century Gothic',
    ]
]);


//_____________________________H____________
//REVIEWED BY
// Find the first empty cell after 'H16'
$emptyCellH = 'H16';
while ($sheet->getCell($emptyCellH)->getValue() != "") {
    $emptyCellH++;
}

$emptyCellH++; // Mag-leave ng isang cell
$emptyCellH++; // Mag-leave ng isang cell
$emptyCellH++; // Mag-leave ng isang cell
$emptyCellH++; // Mag-leave ng isang cell
$emptyCellH++; // Mag-leave ng isang cell
$emptyCellH++; // Mag-leave ng isang cell
$emptyCellH++; // Mag-leave ng isang cell
$emptyCellH++; // Mag-leave ng isang cell
$emptyCellH++; // Mag-leave ng isang cell
$emptyCellH++; // Mag-leave ng isang cell
$sheet->setCellValue($emptyCellH, 'Noted by: ');
$sheet->getStyle($emptyCellH)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 10,
        'name' => 'Century Gothic',
    ]
]);

//DATE
// Find the next empty cell after 'REVIEWED BY
$emptyCellH++;
while ($sheet->getCell($emptyCellH)->getValue() != "") {
    $emptyCellH++;
}

$emptyCellH++;
$emptyCellH++;
$sheet->setCellValue($emptyCellH, 'Date: ');
$sheet->getStyle($emptyCellH)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);


//_____________________________I____________
//NOTED BY ____________
// Find the first empty cell after 'I16'
$emptyCellI = 'I16';
while ($sheet->getCell($emptyCellI)->getValue() != "") {
    $emptyCellI++;
}

$emptyCellI++; // Mag-leave ng isang cell
$emptyCellI++; // Mag-leave ng isang cell
$emptyCellI++; // Mag-leave ng isang cell
$emptyCellI++; // Mag-leave ng isang cell
$emptyCellI++; // Mag-leave ng isang cell
$emptyCellI++; // Mag-leave ng isang cell
$emptyCellI++; // Mag-leave ng isang cell
$emptyCellI++; // Mag-leave ng isang cell
$emptyCellI++; // Mag-leave ng isang cell
$emptyCellI++; // Mag-leave ng isang cell
$sheet->setCellValue($emptyCellI, '________________');
$sheet->getStyle($emptyCellI)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 10,
        'name' => 'Century Gothic',
    ]
]);

//signature
// Find the next empty cell after '____________
$emptyCellI++;
while ($sheet->getCell($emptyCellI)->getValue() != "") {
    $emptyCellI++;
}

$sheet->setCellValue($emptyCellI, 'Signature over printed name');
$sheet->getStyle($emptyCellI)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 8,
        'name' => 'Century Gothic',
    ]
]);

//POSITION
// Find the next empty cell after 'SIGNATURE
$emptyCellI++;
while ($sheet->getCell($emptyCellI)->getValue() != "") {
    $emptyCellI++;
}

$sheet->setCellValue($emptyCellI, 'Dean/Principal');
$sheet->getStyle($emptyCellI)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 10,
        'name' => 'Century Gothic',
    ]
]);

//_______
// Find the next empty cell after '____________
$emptyCellI++;
while ($sheet->getCell($emptyCellI)->getValue() != "") {
    $emptyCellI++;
}

$sheet->setCellValue($emptyCellI, '________________');
$sheet->getStyle($emptyCellI)->applyFromArray([
    'font' => [
        'bold' => false,
        'size' => 10,
        'name' => 'Century Gothic',
    ]
]);





// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PNC-AA-FO-17-ACADEMIC-CONSULTATION-AND-CLASSYEAR-LEVEL-ADVISING-FORM.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1'); // If serving to IE over SSL
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // Always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = new Xlsx($spreadsheet);
ob_end_clean(); // Clear the output buffer to avoid corrupted file
$writer->save('php://output');
exit;
?>
