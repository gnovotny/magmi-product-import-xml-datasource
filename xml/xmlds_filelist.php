<?php
$files = $this->getXMLList();
if ($files !== false && count($files) > 0)
{
    ?>
<select name="XML:filename" id="xmlfile">
	<?php foreach($files as $fname){ ?>	
		<option <?php if($fname==$this->getParam("XML:filename")){?>
		selected=selected <?php }?> value="<?php echo $fname?>"><?php echo basename($fname)?></option>
	<?php }?>
</select>
<a id='xmldl'
	href="./download_file.php?file=<?php $this->getParam("XML:filename")?>">Download
	XML</a>
<script type="text/javascript">
 $('xmldl').observe('click',function(el){
	    var fval=$('xmlfile').value;
 		$('xmldl').href="./download_file.php?file="+fval;}
	);
</script><?php } else {?>
<span> No xml files found in <?php echo $this->getScanDir(false)?></span>
<?php }?>