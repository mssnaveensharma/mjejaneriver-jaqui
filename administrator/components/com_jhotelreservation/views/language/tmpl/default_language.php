<?php

defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
<form action="index.php?tmpl=component&amp;option=<?php echo ACYMAILING_COMPONENT ?>" method="post" name="adminForm"  id="adminForm" autocomplete="off">
	<fieldset class="acyheaderarea">
		<div class="acyheader" style="float: left;"><?php echo JText::_('ACY_FILE',true).' : '.$this->file->name; ?></div>
		<div class="toolbar" id="toolbar" style="float: right;">
			<table><tr>
			<td><a onclick="javascript:submitbutton('save'); return false;" href="#" ><span class="icon-32-save" title="<?php echo JText::_('ACY_SAVE',true); ?>"></span><?php echo JText::_('ACY_SAVE',true); ?></a></td>
			</tr></table>
		</div>
	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo JText::_( 'ACY_FILE',true).' : '.$this->file->name; ?>
		</legend>
		<textarea style="width:700px;" rows="18" name="content" id="translation" ><?php echo @$this->file->content;?></textarea>
	</fieldset>

	<div class="clr"></div>
	<input type="hidden" name="code" value="<?php echo $this->file->name; ?>" />
	<input type="hidden" name="option" value="<?php echo getBookingExtName(); ?>" />
	<input type="hidden" name="task" value="language.saveLanguage" />
	<input type="hidden" name="ctrl" value="file" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>