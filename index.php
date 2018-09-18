<?php
include('inc/head.php');
require_once('inc/functions.php'); // load file containing all of the functions we'll use to upload the csv, make directories, get XML data, etc...


if(isset($_FILES['csv_file'])): // check to make sure a csv file has been uploaded. If not none of the following functions will run.

	$file = basename($_FILES['csv_file']['name']); // get name of uploaded file from $_FILES variable array, pass that to variable $file

	$directory = str_replace(' ', '_', $file); // remove spaces and fill with underscores in file name, use that string to create the directory variable

	$directory = preg_replace('/\\.[^.\\s]{3,4}$/', '', $directory);


	$makeXML = new makeXML($file, $directory); // call makeXML class established in functions.php file. pass global variables $file and $directory to use between functions...

	$makeXML->makeDirectory($file, $directory); // make the directory to hold the generated XML and, temporarily, the uploaded CSV file. CSV file is deleted once the metadata array is generated.

	$csv_file = $directory .'/'. $file; // path to CSV file, to pass to the collectMetadata function

	$schema = 'MODS'; // pass metadata type to collectMetadata, currently this sets MODS as the default. We may add more metadata types as we go...

	$batch_type = $_POST['batch_type'];
	$children = $_POST['children'];
	$children = (int)$_POST['children']; // convert value of $children from text string to integer
	
	$makeXML->collectMetadata($csv_file, $schema, $directory, $batch_type, $children); // Rip some XML! 

endif;
?>


<form method="POST" action="" enctype="multipart/form-data">
				
	<h3>CSV file</h3>
	

	<fieldset class="ingest_type">
		<label>Select the type of batch ingest you want to prepare.</label>
		<select name="batch_type" required="required">
			<option disabled> -- Select type of ingest to prepare -- </option>
			<option value="basic">Basic Batch Ingest</option>
			<option value="compound">Compound Batch Ingest</option>
		</select>
	</fieldset>
	<fieldset class="number_of_children">
		<label>Number of child objects for compound batch
			<div class="note">Please ignore this if you are preparing a basic batch ingest.</div>
			<input type="number" name="children" value="1"/>
		</label>
	</fieldset>
	<fieldset class="file_upload">
	<label>
		Upload the csv file for the collection you are preparing to ingest</label>
		<input type="file" name="csv_file"  required="required" />
	</fieldset>
	<input type="submit" value="submit" />
</form>

<?php
include('inc/footer.php');
?>