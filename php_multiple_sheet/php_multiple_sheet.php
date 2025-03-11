<html>
<head>
<title>ShotDev.Com Tutorial</title>
</head>
<body>
<?
	
	//*** Get Document Path ***//
	$strPath = realpath(basename(getenv($_SERVER["SCRIPT_NAME"]))); // C:/AppServ/www/myphp

	//*** Excel Document Root ***//
	$strFileName = "MyXls/MyExcel.xls";

	//*** Connect to Excel.Application ***//
	$xlApp = new COM("Excel.Application");
	$xlBook = $xlApp->Workbooks->Add();
	$xlSheet1 = $xlBook->Worksheets(1);
	
	$xlApp->Application->Visible = False;	

	//*** Add Sheet 4 ***//
	$xlBook->Sheets->Add; //*** Sheet Default=3 Add New 1 Sheet=4 ***//
	
	//*** Create Sheet 1 ***//
	$xlBook->Worksheets(1)->Name = "My Sheet1";							
	$xlBook->Worksheets(1)->Select;

	//*** Write text to Row 1 Column 1 ***//		
	$xlApp->ActiveSheet->Cells(1,1)->Value = "ShotDev.Com 1";
	
	//*** Write text to Row 1 Column 2 ***//
	$xlApp->ActiveSheet->Cells(1,2)->Value = "Mr.Weerachai Nukitram 1";


	//*** Create Sheet 2 ***//
	$xlBook->Worksheets(2)->Name = "My Sheet2";							
	$xlBook->Worksheets(2)->Select;

	//*** Write text to Row 1 Column 1 ***//		
	$xlApp->ActiveSheet->Cells(1,1)->Value = "ShotDev.Com 2";
	
	//*** Write text to Row 1 Column 2 ***//
	$xlApp->ActiveSheet->Cells(1,2)->Value = "Mr.Weerachai Nukitram 2";

	
	//*** Create Sheet 3 ***//
	$xlBook->Worksheets(3)->Name = "My Sheet3";							
	$xlBook->Worksheets(3)->Select;

	//*** Write text to Row 1 Column 1 ***//		
	$xlApp->ActiveSheet->Cells(1,1)->Value = "ShotDev.Com 3";
	
	//*** Write text to Row 1 Column 2 ***//
	$xlApp->ActiveSheet->Cells(1,2)->Value = "Mr.Weerachai Nukitram 3";


	//*** Create Sheet 4 ***//
	$xlBook->Worksheets(4)->Name = "My Sheet4";							
	$xlBook->Worksheets(4)->Select;

	//*** Write text to Row 1 Column 1 ***//		
	$xlApp->ActiveSheet->Cells(1,1)->Value = "ShotDev.Com 4";
	
	//*** Write text to Row 1 Column 2 ***//
	$xlApp->ActiveSheet->Cells(1,2)->Value = "Mr.Weerachai Nukitram 4";

	$xlBook->Worksheets(1)->Select; //*** Focus Sheet 1 ***//

	@unlink($strFileName); //*** Delete old files ***//	

	$xlBook->SaveAs($strPath."/".$strFileName); //*** Save to Path ***//
	//$xlBook->SaveAs(realpath($strFileName)); //*** Save to Path ***//

	//*** Close & Quit ***//
	$xlApp->Application->Quit();
	$xlApp = null;
	$xlBook = null;
	$xlSheet1 = null;
?>
Excel Created <a href="<?=$strFileName?>">Click here</a> to Download.

</body>
</html>
<!--- This file download from www.shotdev.com -->