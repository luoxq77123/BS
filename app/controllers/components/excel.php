<?php
App::import('Vendor', 'PHPExcel', array('file' => 'phpexcel' . DS . 'PHPExcel.php'));
App::import('Vendor', 'PHPExcel', array('file' => 'phpexcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
App::import('Vendor', 'PHPExcel', array('file' => 'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel5.php'));
App::import('Vendor', 'PHPExcel', array('file' => 'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php'));

App::import('Vendor', 'NumericArray', array('file' => 'someoperation' . DS . 'class.numeric_array.php'));
/**
 * Class ExcelComponent
 *
 * Data to Excel
 * @author CHEN
 *
 */
class ExcelComponent extends Component{

	function beforeRedirect(){
		 
	}

	/**
	 *
	 * 表头信息数组(关联数组，其Key应与数据保持一致)
	 */
	public $outfit=array();

	/**
	 * 数据
	 * @var array
	 */
	public $data = array();

	/**
	 *
	 * 初始化
	 * @param array $_outfit
	 * @param array $_data
	 */
	public function initExcel($_outfit,$_data){
		$this->outfit = $_outfit;
		$this->data = $_data;
	}

	/**
	 *
	 * 导出数据
	 */
	public function outExcel(){
		$objExcel = new PHPExcel();
		//设置相关属性
		$objExcel->getProperties()->setCreator("andy");
		$objExcel->getProperties()->setLastModifiedBy("andy");
		$objExcel->getProperties()->setTitle("Office 2003 XLS Test Document");
		$objExcel->getProperties()->setSubject("Office 2003 XLS Test Document");
		$objExcel->getProperties()->setDescription("Test document for Office 2003 XLS, generated using PHP classes.");
		$objExcel->getProperties()->setKeywords("office 2003 openxml php");
		$objExcel->getProperties()->setCategory("Test result file");
		$objExcel->setActiveSheetIndex(0);

		//设置表头
		$thCount = count($this->outfit);
		if (!$thCount){
			return false;
		}
		$theCellZ = $firstCellZ = 'A';
		$keyCellZ = array();
		foreach ($this->outfit as $cellKey=>$cellValue) {
			$tmpCellName = $theCellZ.'1';
			$tmpCellValue = $cellValue;
				
			$objExcel->getActiveSheet()->setCellValue($tmpCellName, $tmpCellValue);
			$objExcel->getActiveSheet()->getColumnDimension($theCellZ)->setWidth(35);
				
			$keyCellZ[$theCellZ] = $cellKey;
			$theCellZ = chr(ord($theCellZ) + 1);
		}
		$lastCellZ = chr(ord($theCellZ) - 1);


		//表头属性
		$thArea = $firstCellZ.'1:'.$lastCellZ.'1';
		//设置高度以及填充
		$objExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
		$objExcel->getActiveSheet()->getStyle($thArea)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objExcel->getActiveSheet()->getStyle($thArea)->getFill()->getStartColor()->setARGB('ffe2e2e2');
		//设置对齐方式
		$objExcel->getActiveSheet()->getStyle($thArea)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objExcel->getActiveSheet()->getStyle($thArea)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		//设置边框
		$objExcel->getActiveSheet()->getStyle($thArea)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED);
		$objExcel->getActiveSheet()->getStyle($thArea)->getBorders()->getAllBorders()->setColor(new PHPExcel_Style_Color('ffbebebe'));
		//设置文字样式
		$objExcel->getActiveSheet()->getStyle($thArea)->getFont()->setName('arial');
		$objExcel->getActiveSheet()->getStyle($thArea)->getFont()->setSize(11);
		$objExcel->getActiveSheet()->getStyle($thArea)->getFont()->setBold(true);
		$objExcel->getActiveSheet()->getStyle($thArea)->getFont()->getColor()->setARGB('ff666666');

		//填充数据
		$fillData = NumericArray::toNumericArray($this->data);
		$num = 2;
		foreach ($fillData as $tempData) {
			//设置数据行颜色
			if ($num%2 == 0){
				$fillColor = 'fff5f5f5';
			}
			else {
				$fillColor = 'ffffffff';
			}
				
			foreach ($keyCellZ as $tCellZ=>$tCellKey) {
				$tmpCellDataKey = $tCellZ.$num;
				$tmpCellDataValue = $tempData[$tCellKey];
				$objExcel->getActiveSheet()->setCellValue($tmpCellDataKey, $tmpCellDataValue);
			}

			$dataArea = $firstCellZ.$num.':'.$lastCellZ.$num;
			//设置行属性
			$objExcel->getActiveSheet()->getRowDimension($num)->setRowHeight(30);
			$objExcel->getActiveSheet()->getStyle($dataArea)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$objExcel->getActiveSheet()->getStyle($dataArea)->getFill()->getStartColor()->setARGB($fillColor);
			//设置对齐方式
			$objExcel->getActiveSheet()->getStyle($dataArea)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objExcel->getActiveSheet()->getStyle($dataArea)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

			//设置边框
			$objExcel->getActiveSheet()->getStyle($dataArea)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED);
			$objExcel->getActiveSheet()->getStyle($dataArea)->getBorders()->getAllBorders()->setColor(new PHPExcel_Style_Color('ffbebebe'));

			//设置文字样式
			$objExcel->getActiveSheet()->getStyle($dataArea)->getFont()->setName('arial');
			$objExcel->getActiveSheet()->getStyle($dataArea)->getFont()->setSize(11);
			$objExcel->getActiveSheet()->getStyle($dataArea)->getFont()->getColor()->setARGB('ff666666');

			$num++;
		}

		// 设置页方向和规模
		$objExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
		$objExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objExcel->setActiveSheetIndex(0);
		$timestamp = time();

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="export'.$timestamp.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}