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

function word2pdf_linux($source_file, $output_file){
	// $result =  `unoconv --format pdf --output "$output_file" "$source_file" 2>&1`;
	exec('unoconv --format pdf --output '.$output_file.' '.$source_file);
    if(file_exists($output_file) && filesize($output_file) > 0){
        return true;
    }else{
        return false;
    }
}

function pdf2swf_linux($source_file, $output_file){
	// $command = "/usr/swftools/bin/pdf2swf $source_file -o $output_file 2>&1";
	//$result = `$command`;
    $command = "/usr/swftools/bin/pdf2swf -f -T 9 $source_file -o $output_file ";
    exec($command);
	if(file_exists($output_file) && filesize($output_file) > 0){
		return true;
	}
	return false;

}

function word2swf_linux($word_filepath, $pdf_filepath, $swf_filepath){
    if(word2pdf_linux($word_filepath, $pdf_filepath)){
        if(pdf2swf_linux($pdf_filepath, $swf_filepath)){
            return true;
        }
		failure('转换swf失败，请联系开发人员');
		exit;
    }
    failure('转换pdf失败，请联系开发人员');
    exit;
}
