# PPL BATCH CSV to XML

A php script to generate a batch of xml files from an uploaded csv file. This script can also handle organizing xml and related images into structured directories for compound batch ingest

CSV File Preparation:
------
__The first row of your CSV file needs to have the following headers in this order:__

| Dublin Core:Identifier | Files:File 1 | Files:File 2 | Dublin Core:Title | Dublin Core:Creator | Dublin Core:Contributor | Dublin Core:Publisher | Date Qualifier | Dublin Core:Date | Dublin Core:Description | Dublin Core:Subject | Dublin Core:Subject Heading | Dublin Core:Spatial Coverage | geolocation:address | Dublin Core:Date Created | Item Type Metadata:Width | Item Type Metadata:Height | Item Type Metadata:Caption | Item Type Metadata:Transcription | Dublin Core:Type | Dublin Core:Format | Dublin Core:Rights | Physical Location | Finding Aide Location |

__If the columns are out of order, or columns are missing, the data in the csv will be incorrectly mapped to the MODS XML file(s).__

I have included a sample csv in this repo to use for formatting.

---

Installing and running the script on a local server
======

MAMP
------

Clone or download this repo. Navigate to the root directory of your MAMP install _(typically the htdocs folder)_.

Copy the xml_batch directory into your root and then load the script in a browser. 
Ex. URL: _http://localhost:8888/xml_batch/_

Follow the instructions for using the script remotely.

WAMP
------

Same as above, except, WAMP usually uses the www directory for the root.
Ex. URL: _http://localhost/xml_batch/_


Running the script
======
The script allows you to generate XML files from CSV in either a basic batch or a compound batch.

Basic Batch
------
Selecting basic batch will make one XML record for each row of data (excluding the first row, which contains the column names). The XML file will take the name of the identifier (the data in first column).

The generated XML files will be saved to a directory that is named after the CSV file you uploaded via the form.
If you want to also collect the image associated with an XML record please put the image(s) in the images directory. 

Images must share the same name as the XML file it is associated with. If an image exists in the images directory and it doesn't have a corresponding XML file the image will not be copied.

The generated directory and files can be ingested into islandora by using the islandora batch ingest module.  You can use either the GUI available in the Drupal admin, or you can ingest via drush.

*I find ingesting via drush to be faster and less prone to issues with server timeouts. If you are planning to ingest a large volume of files I would use drush*

For more information please see the github repository for [Islandora Batch](https://github.com/Islandora/islandora_batch)

```
|-- csv_file_name (parent directory)
	VM001.xml
	VM001.jpg

```

Compound Batch
------
Selecting Compound batch will produce a directory, named after the CSV file, with a series of sub-directories. Each sub-directory will take the name of the identifier. Inside each sub-directory are directories for each child object related to the XML record. The XML file must be named MODS.xml. The name of the image should correspond to the name of the identifer appended with an underscore and a number.  

For example, an XML record for an image with a front and back (2 images), will have the following directory structure:
```
|-- csv_file_name (parent directory)
	|-- VM001
		|-- OBJ_1
			MODS.xml
			VM001_1.jpg
		|-- OBJ_2
			MODS.xml
			VM001_2.jpg
		MODS.xml
		structure.xml
	|-- VM002
		|-- OBJ_1
			MODS.xml
			VM002_1.jpg
		|-- OBJ_2
			MODS.xml
			VM002_2.jpg
		MODS.xml
		structure.xml
```

The generated XML file in a compound batch will be named MODS.xml. A structure XML file is also generated. This maps the each contents of the directory to the MODS.xml file.

```
<?xml version="1.0" encoding="UTF-8"?>
<islandora_compound_object title="VM001"><child content="VM001/OBJ_1"/><child content="VM001/OBJ_2"/></islandora_compound_object>
``` 

The structure file is used by the islandora_compound_batch module to prepare the contents of each directory for ingest. [For more information see the github repository for the Islandora Compound Batch module](https://github.com/MarcusBarnes/islandora_compound_batch)

This script can be reconfigured for other ingest workflows. Please review [Islandora Import Cookbook](https://github.com/MarcusBarnes/mik/wiki/Cookbook:-Importing-your-packages-into-Islandora) for different methods of batch importing content into various Islandora Content models.

Notes / Possible Issues
------

**The script hangs after I submit my CSV file**

This could be due to the amount of XML files you're trying to generate, or maximum execution time in your php.ini settings.  Try breaking large CSV files into chunks or increasing the maximum execution time.

**The data is mapped incorrectly in the XML files**

Make sure the first row of your CSV file has the headers in the order specified above. Missing headers or columns that are not in the correct order will not map correctly.

**I'm having some other problem**

Report the bug on this repo and I'll look into it.




