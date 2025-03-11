<?php
class WorkSheet
{
    private $lines = array();
    public $sWorksheetTitle;
    public function __construct($sWorksheetTitle)
    {
        $this->setWorksheetTitle($sWorksheetTitle);
    }
    public function setWorksheetTitle ($title)
    {
        $title = preg_replace ("/[\\\¦:¦\/¦\?¦\*¦\[¦\]]/", "", $title);
        $title = substr ($title, 0, 31);
        $this->sWorksheetTitle = $title;
    }
    public function addRow ($array)
    {
        $cells = "";
        foreach ($array as $k => $v){
                $type = 'String';
                $v = htmlentities($v, ENT_COMPAT, "UTF-8");
                $cells .= "<Cell><Data ss:Type=\"$type\">" . $v . "</Data></Cell>\n";
        }
        $this->lines[] = "<Row>\n" . $cells . "</Row>\n";
    }
    public function printline()
    {
        foreach ($this->lines as $line)
        {
            echo $line;
        }
    }
}
class Excel
{
    public $worksheets = array();
    public function __construct($sWorksheetTitle)
    {
        $this->addsheet($sWorksheetTitle);
    }
    public function addsheet($title)
    {
        $this->worksheets[$title] = new WorkSheet($title);
    }
    public function generate ($filename = 'excel-export')
    {
        $filename = preg_replace('/[^aA-zZ0-9\_\-]/', '', $filename);
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: inline; filename=\"" . $filename . ".xls\"");
        echo stripslashes("<?xml version=\"1.0\" encoding=\"UTF-8\"?\>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">");
        foreach ($this->worksheets as $worksheet)
        {
            echo "\n<Worksheet ss:Name=\"" . $worksheet->sWorksheetTitle . "\">\n<Table>\n";
            $worksheet->printline();
            echo "</Table>\n</Worksheet>\n";
        }
        echo "</Workbook>";
    }
}
?>