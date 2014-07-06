
<div class="plugin_description">
	This plugin enables magmi import from xml files (using Dataflow format
	+ magmi extended columns)<br />
</div>
<div>

	<div class="xmlmode"></div>

	<ul class="formline">
		<li class="label">XML import mode</li>
		<li class="value"><select name="XML:importmode" id="XML:importmode">
				<option value="local"
					<?php if($this->getParam("XML:importmode","local")=="local"){?>
					selected="selected" <?php }?>>Local</option>
				<option value="remote"
					<?php if($this->getParam("XML:importmode","local")=="remote"){?>
					selected="selected" <?php }?>>Remote</option>
		</select>
	
	</ul>

	<div id="localxml"
		<?php if($this->getParam("XML:importmode","local")=="remote"){?>
		style="display: none" <?php }?>>
		<ul class="formline">
			<li class="label">XMLs base directory</li>
			<li class="value"><input type="text" name="XML:basedir"
				id="XML:basedir"
				value="<?php echo $this->getParam("XML:basedir","var/import")?>"></input>
				<div class="fieldinfo">Relative paths are relative to magento base
					directory , absolute paths will be used as is</div></li>
		</ul>
		<ul class="formline">
			<li class="label">File to import:</li>
			<li class="value" id="xmlds_filelist">
 <?php echo $this->getOptionsPanel("xmlds_filelist.php")->getHtml(); ?>
 </li>
		</ul>
	</div>

	<div id="remotexml"
		<?php if($this->getParam("XML:importmode","local")=="local"){?>
		style="display: none" <?php }?>>
		<ul class="formline">
			<li class="label">Remote XML url</li>
			<li class="value"><input type="text" name="XML:remoteurl"
				id="XML:remoteurl"
				value="<?php echo $this->getParam("XML:remoteurl","")?>"
				style="width: 400px"></input> <input type="checkbox"
				id="XML:forcedl" name="XML:forcedl"
				<?php if($this->getParam("XML:forcedl",false)==true){?>
				checked="checked" <?php }?>>Force Download</li>
		</ul>

		<div id="remotecookie">
			<ul class="formline">
				<li class="label">HTTP Cookie</li>
				<li class="value"><input type="text" name="XML:remotecookie"
					id="XML:remotecookie"
					value="<?php echo $this->getParam("XML:remotecookie","")?>"
					style="width: 400px"></li>
			</ul>
		</div>
		<input type="checkbox" id="XML:remoteauth" name="XML:remoteauth"
			<?php  if($this->getParam("XML:remoteauth",false)==true){?>
			checked="checked" <?php }?>>authentication needed
		<div id="remoteauth"
			<?php  if($this->getParam("XML:remoteauth",false)==false){?>
			style="display: none" <?php }?>>
			<div class="remoteuserpass">
				<ul class="formline">
					<li class="label">User</li>
					<li class="value"><input type="text" name="XML:remoteuser"
						id="XML:remoteuser"
						value="<?php echo $this->getParam("XML:remoteuser","")?>"></li>

				</ul>
				<ul class="formline">
					<li class="label">Password</li>
					<li class="value"><input type="text" name="XML:remotepass"
						id="XML:remotepass"
						value="<?php echo $this->getParam("XML:remotepass","")?>"></li>
				</ul>
			</div>

		</div>

	</div>


</div>
<div>
	<h3>XML options</h3>
	<span class="">XML parent element:</span><input type="text"
		name="XML:elementparent"
		value="<?php echo $this->getParam("XML:elementparent")?>"></input> <span
		class="">XML element name:</span><input type="text"
		name="XML:elementname"
		value='<?php echo $this->getParam("XML:elementname")?>'></input>
</div>


<script type="text/javascript">
	handle_auth=function()
	{
		if($('XML:remoteauth').checked)
		{
			$('remoteauth').show();	
		}
		else
		{
			$('remoteauth').hide();
		}
	}
	
	$('XML:basedir').observe('blur',function()
			{
			new Ajax.Updater('xmlds_filelist','ajax_pluginconf.php',{
			parameters:{file:'xmlds_filelist.php',
						plugintype:'datasources',
					    pluginclass:'<?php echo get_class($this->_plugin)?>',
					    profile:'<?php echo $this->getConfig()->getProfile()?>',
					    'XML:basedir':$F('XML:basedir')}});
			});

	$('XML:importmode').observe('change',function()
			{
				if($F('XML:importmode')=='local')
				{
					$('localxml').show();
					$('remotexml').hide();
				}
				else
				{
					$('localxml').hide();
					$('remotexml').show();
				}
			});
	$('XML:remoteauth').observe('click',handle_auth);
	$('XML:remoteurl').observe('blur',handle_auth);
</script>
