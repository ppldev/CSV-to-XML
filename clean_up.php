<?php

$contents;
$directory = 'to_batch/';

foreach(glob($directory.'*') as $file):
	if(is_dir($file)):
		foreach(glob($file.'/*') as $f):
			$f = explode('/', $f);
			
			$filecheck = $f[2];
			$filename = explode('.', $filecheck);
			$filename = $filename[0];

			if(!file_exists($file.'/'.$filename.'.xml')):
				unlink($file.'/'.$filename.'.jpg');
				echo $filename.'.jpg has been deleted <br>';
			endif;
			
		endforeach;
	endif;
endforeach;
?>

<form method="POST">

</form>