
<?php

//echo "RRRRRRRRRRRRRRRRRR"; 
//exit("ggggggggg");
// database record to be exported
//$db_record = 'catlmfgspecmaster';

// optional where query
$where = 'WHERE 1 ORDER BY 1';
// filename for export
$csv_filename = $mfn.date('Y-m-d').'.csv';



$csv_export = '';
// query to get data from database
$query = mysql_query("SELECT * FROM ".$db_record." ".$where);
$field = mysql_num_fields($query);
// create line with field names
for($i = 0; $i < $field; $i++) {
  $csv_export.= mysql_field_name($query,$i).',';
}
// newline (seems to work both on Linux & Windows servers) 
$csv_export.= '
';
while($row = mysql_fetch_array($query)) {
  // create line with field values
  for($i = 0; $i < $field; $i++) {
    $csv_export.= '"'.$row[mysql_field_name($query,$i)].'",';
  }	
  $csv_export.= '
';	
}
 //Export the data and prompt a csv file for download
header("Content-type: text/x-csv");
header("Content-Disposition: attachment; filename=".$csv_filename."");
echo($csv_export);
?>