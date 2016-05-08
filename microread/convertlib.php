<?php 
/**doc,ppt,txt转pdf */
function word2pdf($docfileurl,$pdfurl,$swfurl){
	$osm = new COM("com.sun.star.ServiceManager")or die ("请确认OpenOffice.org库是否已经安装.\n");
	$args = array(MakePropertyValue("Hidden",true,$osm));
	$oDesktop = $osm->createInstance("com.sun.star.frame.Desktop");
	$oWriterDoc = $oDesktop->loadComponentFromURL($docfileurl,"_blank", 0, $args);
	$export_args = array(MakePropertyValue("FilterName","writer_pdf_Export",$osm));
	$oWriterDoc->storeToURL('file:///'.$pdfurl,$export_args);
	$oWriterDoc->close(true);
	pdf2swf($pdfurl,$swfurl);
}

function MakePropertyValue($name,$value,$osm){
	$oStruct=$osm->Bridge_GetStruct("com.sun.star.beans.PropertyValue");
	$oStruct->Name = $name;
	$oStruct->Value = $value;
	return $oStruct;
}

//pdf转swf
function pdf2swf($oldfile,$newfile){
    // $command = "D:/SWFTools/pdf2swf.exe $newfile -o $oldfile";
	$command = "D:/SWFTools/pdf2swf.exe -o $newfile $oldfile";
	// $command = "D:/SWFTools/pdf2swf.exe -f -T 9 $newfile -o $oldfile";
    exec($command);//执行转换
    return $newfile;
}

