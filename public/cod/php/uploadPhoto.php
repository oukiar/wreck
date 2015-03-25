<?

if ($_FILES["inputPhoto"]["error"] > 0) 
{
	print '<script type="text/javascript">'; 
	print 'alert("No File Selected ")'; 
	print '</script>'; 
    print '<META HTTP-EQUIV="Refresh" Content="0; URL='.$location.'">';
} 
else
{
    /*
    echo "Upload: " . $_FILES["profileimg"]["name"] . "<br>";
    echo "Type: " . $_FILES["profileimg"]["type"] . "<br>";
    echo "Size: " . $_FILES["profileimg"]["size"] . " kB<br>";
    echo "Stored in: " . $_FILES["profileimg"]["tmp_name"];
    * */
    
    
    $usrID = $_SESSION["usrID"];
    
    /*
     * Oscar Alcantara [1]:
     * 
     * Eliminar imagen anterior
     * */
    $imgfile = get_value("usrTable", "profilepicture", "usrID", $usrID);
    
    if($imgfile != "" && $imgfile != -1)
    {
        //eliminar archivo de imagen anterior del profile
        unlink("../../ppic/" . $imgfile);
    }
    
    
    
    
    $filename = $_FILES["profileimg"]["name"];
    
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if( !in_array($ext, array('jpg', 
                                'gif',
                                'png')   
                                ) )
    {
        header("location: ../../web/admin.php?error=Invalid image format (only JPG, GIF and PNG)");
        exit();
    }
    
    $uploaded = $_FILES['profileimg']['tmp_name'];
    
  
    $dest = "../../ppic/$usrID.jpg";
    //$dest = "$usrID.jpg";
  
    //$dest = $_SERVER['DOCUMENT_ROOT'] . "ppic/";
    
    //if( is_writable($dest) )
    //    echo "writableee<br>";
  
    if (move_uploaded_file($uploaded, $dest))
    {
        $profileimage = $usrID . "_thumbnail". strval(rand()) .".jpg";
        $thumbnail = "../../ppic/" . $profileimage;
    
        // Get new sizes
        list($width, $height) = getimagesize($dest);
        
        // checar el lado mas pequeÃ±o
        if($width < $height)
        {
            $wside = $width;
            
            $xsrc = 0;
            $ysrc = ($height/2) - ($wside/2);
         }   
        else
        {
            $wside = $height;
        
            $xsrc = ($width/2) - ($wside/2);
            $ysrc = 0;
        }

        //crear thumbnail cuadrado
        $thumb = imagecreatetruecolor(224, 224);
        
        //load image
        
        if($ext == 'png')
        {
            $source = imagecreatefrompng($dest);
            
        }
        else if($ext == 'gif')
        {
            $source = imagecreatefromgif($dest);
            
        }
        else
        {
            $source = imagecreatefromjpeg($dest);
        }

        // Resize
        imagecopyresized($thumb, $source, 0, 0, $xsrc, $ysrc, 224, 224, $wside, $wside);

        // Output
        imagejpeg($thumb, $thumbnail);
        
        //eliminar imagen original
        unlink($dest);
        
        /*
         * Oscar Alcantara [1]:
         * - Almacenar el nombre de la imagen de perfil en la base de datos
         * */
        mysql_query("update usrTable set profilepicture='$profileimage' where usrID=$usrID");
        
        header("location: ../../web/admin.php?msg=Upload successfull !");
    
    } 
    else 
    {        
        echo "uploaded: $uploaded<br>dest:$dest";
		
        
        //header("location: ../../web/admin.php?error=Error [$uploaded] uploading picture $dest");
    }

  
}

function page_redirect($location)
 {
   echo '<META HTTP-EQUIV="Refresh" Content="0; URL='.$location.'">';
   exit; 
 }
?>
