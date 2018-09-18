<?php

class makeXML 
{
	// establish variables to pass between different functions we will use to save CSV, create a directory for our XML, and create our XML files...

	protected $_file; // variable for csv file, we'll use this to get the file name, and get the data from the file
	protected $_directory; // this will be the unique directory we'll establish to save all of our XML files.

	public function __construct($file, $directory)
	{
		$file = $this->_file;
		$directory = $this->_directory;
	}

	public static function makeDirectory($file, $directory)
	{
		// Pass the name of the csv file and create a directory with that name. If there is an issue, throw up an error.

		if(!file_exists($directory)):
			mkdir($directory, 0777, true);
			$csv_file = $directory .'/'. $file;

			if(!file_exists($csv_file)):
				if(move_uploaded_file($_FILES['csv_file']['tmp_name'], $csv_file)):
				else:
					echo 'There was a problem uploading your file. Please go back and try again.';
				endif;
			endif;

		elseif(file_exists($directory)):

		else:
			echo 'There was a problem creating the directory '.$directory.'.';
		endif;
	}

	public static function collectMetadata($csv_file, $schema, $directory, $batch_type, $children) 
	{ // Get the csv file, read the data in each row, spit into an array - $metadata.  

			$metadata = array();
			$row = 0;
			$counter = 0;
			$flag = true;
			$schema_uri = 'http://www.loc.gov/mods/v3';
			
			ini_set('auto_detect_line_endings', TRUE);

			if( ( $handle = fopen($csv_file, 'r')) !== FALSE) {
				while(($data = fgetcsv($handle, 0, ',', '"', '"')) !== FALSE) {

					if($row == 0) {
						$headerValues = $data;

						var_dump($data[0]);

					}
					
					if($flag) { $flag = false; continue; }
					$num = count($data);
					
					$metadata[] = array(
					'identifier' 	=> $data[0],
					'file_1'		=> $data[1],
					'file_2'		=> $data[2],
					'title'		=>  $data[3],
					'creator'		=>  $data[4],
					'contributor'	=> $data[5],
					'publisher'	=> $data[6],
					'date_qualifer'	=> $data[7],
					'date'		=> $data[8],
					'description'	=> $data[9],
					'subject'		=> $data[10],
					'subject_name'	=> $data[11],
					'spatial_coverage'	=> $data[12],
					'geolocation_address' => $data[13],
					'date_created'	 => $data[14],
					'width'		=> $data[15],
					'height'		=> $data[16],
					'caption'		=> $data[17],
					'transcription'	=> $data[18],
					'type'		=> $data[19],
					'format'		=> $data[20],
					'rights'		=> $data[21],
					'physical_location'	=>$data[22],
					'finding_aid'	=> $data[23]
					);
								
				}


				fclose($handle);
			}

			if(!file_exists($directory)):
				mkdir($directory, 0777); // Make the XML directory if it does not already exist. 
			endif;


			for($i=0;$i<count($metadata);$i++) { 

			
			$mods = new \SimpleXMLElement('<mods/>'); // Make the XML using SimpleXMLElement class
			
				
					// generate xml element and requried namespaces
					$mods->addAttribute('xmlns', $schema_uri);
					$mods->addAttribute('xmlns:xmlns:mods', $schema_uri);
					$mods->addAttribute('xmlns:xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
					$mods->addAttribute('xmlns:xmlns:xlink', 'http://www.w3.org/1999/xlink');

					//generate mods child nodes
					$identifier = $mods->addChild('identifier', htmlspecialchars($metadata[$i]['identifier']));
					$identifier->addAttribute('type', 'local');

					$title = $mods->addChild('titleInfo');
					$title->addChild('title', htmlspecialchars($metadata[$i]['title']));
					
					if($metadata[$i]['creator'] != '') {
						$creator = $mods->addChild('name');
						$creator->addChild('namePart', htmlspecialchars($metadata[$i]['creator']));
						$role = $creator->addChild('role');
						$role->addChild('roleTerm', 'Creator');
						$role->addAttribute('type', 'text');
					}
					
					if($metadata[$i]['contributor'] != ''){
						$contributor = $mods->addChild('name');
						$contributor->addChild('namePart', htmlspecialchars($metadata[$i]['contributor']));
						$contrib_role = $contributor->addChild('role');
						$contrib_role->addChild('roleTerm', 'Former Owner');
						$contrib_role->addAttribute('authority', 'dnr');
						$contrib_role->addAttribute('type', 'text');
					}
					

					$originInfo = $mods->addChild('originInfo');
					$publisher = $originInfo->addChild('publisher', htmlspecialchars($metadata[$i]['publisher']));
					
					$date_qualifer = $originInfo->addChild('dateOther', htmlspecialchars($metadata[$i]['date_qualifer']));
					$date = $originInfo->addChild('dateCreated',  htmlspecialchars($metadata[$i]['date']));
					$date_created = $originInfo->addChild('dateCaptured', htmlspecialchars($metadata[$i]['date_created']));

					$description = $mods->addChild('abstract', htmlspecialchars($metadata[$i]['description']));
					$subject = $mods->addChild('subject');


					$topics = $metadata[$i]['subject'];
					$subject->addChild('topic', htmlspecialchars($topics));

					$subject_name = $subject->addChild('name', htmlspecialchars($metadata[$i]['subject_name']));

					$spatial_coverage = $subject->addChild('geographic', htmlspecialchars($metadata[$i]['spatial_coverage']));

					$geolocation_address = $mods->addChild('note', htmlspecialchars($metadata[$i]['geolocation_address']));
					$geolocation_address->addAttribute('type', 'locality');
					$geolocation_address->addAttribute('displayLabel', 'Locality');
					
					$obj_info = $mods->addChild('physicalDescription');
					
					if($metadata[$i]['width'] != '' && $metadata[$i]['height'] != '') {
						
						$extent = $obj_info->addChild('extent', htmlspecialchars('Dimensions: '.$metadata[$i]['width'].' x '.$metadata[$i]['height'].' in'));
						$extent->addAttribute('unit', 'inches');
					}
					

					$note_caption = $obj_info->addChild('note', htmlspecialchars($metadata[$i]['caption']));
					$note_caption->addAttribute('displayLabel', 'Caption');

					$note_transcription = $obj_info->addChild('note', htmlspecialchars($metadata[$i]['transcription']));
					$note_transcription->addAttribute('displayLabel', 'Transcription');

					$format = $obj_info->addChild('internetMediaType', htmlspecialchars($metadata[$i]['format']));

					$type = $mods->addChild('typeOfResouce', htmlspecialchars($metadata[$i]['type']));

					$rights = $mods->addChild('accessCondition', htmlspecialchars($metadata[$i]['rights']));
					$location = $mods->addChild('location');
					$location->addChild('physicalLocation', htmlspecialchars($metadata[$i]['physical_location']));

					if($batch_type == 'compound'):
						// generate structure file for compound batch
						$structure = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><islandora_compound_object/>');
						$structure->addAttribute('title', htmlspecialchars($metadata[$i]['identifier']));

						for($a=1; $a<=$children; $a++) {
							$child = $structure->addChild('child');
							$child->addAttribute('content', htmlspecialchars($metadata[$i]['identifier']).'/OBJ_'.$a);	
						}
					endif;

				$identifier = $metadata[$i]['identifier'];
				
				// if this is a compound batch generate the directory structure and copy the required files
				if($batch_type == 'compound'):
					
					$parentDirectory = $directory .'/'.$identifier;
					mkdir($parentDirectory, 0777, true);
					
					$imgDir = 'images/';
					
					$c;
					
					if(file_exists($parentDirectory)) {
						
						$mods->asXML($parentDirectory.'/MODS.xml');
						$structure->asXML($parentDirectory.'/structure.xml');

						for($c=1;$c<=$children;$c++) {
							$xml_file_path = $directory.'/'.$identifier.'/OBJ_'.$c;
							mkdir($xml_file_path, 0777, true);
							
							if(file_exists($directory.'/OBJ_'.$c)):
								rmdir($directory.'/OBJ_'.$c);
							endif;

							$mods->asXML($xml_file_path.'/MODS.xml');

							if(file_exists($directory.'/MODS.xml')):
								unlink($directory.'/MODS.xml');
								unlink($directory.'/structure.xml');
							endif;
							
							if(file_exists('images/'.trim($identifier).'-'.$c.'.jpg')):
								copy('images/'.trim($identifier).'-'.$c.'jpg', $xml_file_path.'/'.$identifier.'-'.$c.'jpg');	
							endif;
						}
					
					}

				endif;

				// if this is a basic batch save the generated xml and move the corresonding image to the collection directory

				if($batch_type == 'basic'):
						$xml_file_path = $directory.'/' . $identifier.'.xml';

						$mods->asXML($xml_file_path);

						if(file_exists('images/'.$identifier.'.jpg')):
							copy('images/'.$identifier.'.jpg', $directory.'/'.$identifier.'.jpg');
						endif;
				endif;

			
				
		}


		if(file_exists($csv_file)):
			unlink($csv_file); // Delete csv file before we make the zip folder to download. 
		endif;

		// Uncomment below to produce a zipped folder containing the XML, images, etc... NOTE, I haven't tested this out with the new functionality (compound batch option). This may require some retooling...
		
		/*$zip = new \ZipArchive; // make a new zip archive

		$zip->open($directory.'.zip', ZipArchive::CREATE); // create the zip folder

		foreach(glob($directory.'/*') as $file) { // read directory (the one holding the XML files)...
			$zip->addFile($file); // spit those XML files into the zip folder
		}
		$zip->close(); // close it up!

		$zipDownload = $directory.'.zip';

		// set up headers to download the zip file to your computer...
		header("Content-type: application/zip");
		header("contente-Disposition: attachment; filename=".$zipDownload);
		header("Pragma: no-cache");
		header("Expires: 0");
		readfile($zipDownload);
		exit; // Adios!*/
	}

}


?>