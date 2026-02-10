<?php

require_once "./PHPExcel_1.8.0/Classes/PHPExcel.php"; // PHPExcel.php을 불러와야 하며, 경로는 사용자의 설정에 맞게 수정해야 한다.

$objPHPExcel = new PHPExcel();

require_once "./PHPExcel_1.8.0/Classes/PHPExcel/IOFactory.php"; // IOFactory.php을 불러와야 하며, 경로는 사용자의 설정에 맞게 수정해야 한다.

$filename = './testA.xlsx'; // 읽어들일 엑셀 파일의 경로와 파일명을 지정한다.

try {

  // 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.

    $objReader = PHPExcel_IOFactory::createReaderForFile($filename);

    // 읽기전용으로 설정

    $objReader->setReadDataOnly(true);

    // 엑셀파일을 읽는다

    $objExcel = $objReader->load($filename);

    // 첫번째 시트를 선택

    $objExcel->setActiveSheetIndex(0);

    $objWorksheet = $objExcel->getActiveSheet();

    $rowIterator = $objWorksheet->getRowIterator();

    foreach ($rowIterator as $row) { // 모든 행에 대해서

               $cellIterator = $row->getCellIterator();

               $cellIterator->setIterateOnlyExistingCells(false); 

    }

    $maxRow = $objWorksheet->getHighestRow();

    for ($i = 0 ; $i <= $maxRow ; $i++) {

               $name = $objWorksheet->getCell('A' . $i)->getValue(); // A열

               $addr1 = $objWorksheet->getCell('B' . $i)->getValue(); // B열

               $addr2 = $objWorksheet->getCell('C' . $i)->getValue(); // C열

               $addr3 = $objWorksheet->getCell('D' . $i)->getValue(); // D열

               $addr4 = $objWorksheet->getCell('E' . $i)->getValue(); // E열

            $reg_date = $objWorksheet->getCell('F' . $i)->getValue(); // F열

               $reg_date = PHPExcel_Style_NumberFormat::toFormattedString($reg_date, 'YYYY-MM-DD'); // 날짜 형태의 셀을 읽을때는 toFormattedString를 사용한다.

      }

} 

 catch (exception $e) {

    echo '엑셀파일을 읽는도중 오류가 발생하였습니다.';

}

​?>