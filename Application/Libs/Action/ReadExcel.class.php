<?php
namespace Libs\Action;
Vendor('PHPExcel.PHPExcel.IOFactory');

class ReadExcel{
    /**
     * @var
     */




    public function __construct($file_absolute_path="")
    {
        if($file_absolute_path){
            if (file_exists($file_absolute_path)) {//如果文件不存在，则直接return
                // 载入当前文件
                $phpExcel = PHPExcel_IOFactory::load($file_absolute_path);
                // 设置为默认表
                $phpExcel->setActiveSheetIndex(0);
                // 获取表格数量
                $sheetCount = $phpExcel->getSheetCount();
                // 获取行数
                $row = $phpExcel->getActiveSheet()->getHighestRow();
                // 获取列数
                $column = $phpExcel->getActiveSheet()->getHighestColumn();
            }else{
                return;
            }
        }
    }


    public function getExcelData(){

        echo "<br/>表格数目为：$sheetCount" . "<br/>表格的行数：$row" . "<br/>列数：$column";
        $data = [];
        // 行数循环
        for ($i = 1; $i <= $row; $i++) {
            // 列数循环
            for ($c = 'A'; $c <= $column; $c++) {
                $data[] = $phpExcel->getActiveSheet()->getCell($c . $i)->getValue();
            }
            //dump($data);
        }
        return $data;
    }


}