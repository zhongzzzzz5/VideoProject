<?php
namespace Home\Controller;

use PHPExcel_IOFactory;

class PHPExcelController extends \Think\Controller{
    public function main(){
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $fileName = WORKING_PATH."/Public/resource/1.xlsx";
        if (file_exists($fileName)) {
            echo "文件存在！";
        }
        // 载入当前文件
        $phpExcel = PHPExcel_IOFactory::load($fileName);
        // 设置为默认表
        $phpExcel->setActiveSheetIndex(0);
        // 获取表格数量
        $sheetCount = $phpExcel->getSheetCount();
        // 获取行数
        $row = $phpExcel->getActiveSheet()->getHighestRow();
        // 获取列数
        $column = $phpExcel->getActiveSheet()->getHighestColumn();
        echo "<br/>表格数目为：$sheetCount" . "<br/>表格的行数：$row" . "<br/>列数：$column";
        $data = [[]];
        // 行数循环
        for ($i = 1; $i <= $row; $i++) {
            // 列数循环
            for ($c = 'A'; $c <= $column; $c++) {
                $data[$i][$c] = $phpExcel->getActiveSheet()->getCell($c . $i)->getValue();
            }
            //dump($data);
        }
        dump($data);
    }
}