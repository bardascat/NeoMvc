<? $url = 'http://localhost/ckeditor/_samples/browser/java.jpg'; $error="Eroare";
include('SimpleImage.php');
$image = new SimpleImage();
// Required: anonymous function reference number as explained above.
$funcNum = $_GET['CKEditorFuncNum'] ;
// Optional: instance name (might be used to load a specific configuration file or anything else).
$CKEditor = $_GET['CKEditor'] ;
// Optional: might be used to provide localized messages.
$langCode = $_GET['langCode'] ;
 
$message = '';
	$folderName="uploads";
	$pozaUploadata=$_FILES['upload']['name'];
	if(!is_dir($folderName))
	mkdir($folderName);
	move_uploaded_file($_FILES['upload']['tmp_name'],$folderName.'/'.$pozaUploadata);
	if($pozaUploadata!=""){
						$image->load($folderName.'/'.$pozaUploadata);
						$image->resizePerfect(600,600);
						$image->save($folderName.'/'.$pozaUploadata);
						
	}
	$url="http://localhost/online_shop/controller/uploader/uploads/".$pozaUploadata;
	
echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
?>