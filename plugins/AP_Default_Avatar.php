<?php

/**
 * Copyright (C) 2008-2010 Justgizzmo.com
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */

// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('PUN_PLUGIN_LOADED', 1);

// the url to this plugin page.
$plugin_url = $pun_config['o_base_url'].'/admin_loader.php?plugin=AP_Default_Avatar.php';

// Load the profile.php language file
require PUN_ROOT.'lang/'.$admin_language.'/profile.php';

// Load this plugins language file
if (file_exists(PUN_ROOT.'/lang/'.$admin_language.'/default_avatar_plugin.php'))
	require PUN_ROOT.'/lang/'.$admin_language.'/default_avatar_plugin.php';
else
	require PUN_ROOT.'/lang/English/default_avatar_plugin.php';

if ($pun_config['o_avatars'] == '0')
	message($lang_profile['Avatars disabled']);

// lets start
if (isset($_POST['upload_member']))
{
	if (!isset($_FILES['req_member_file']))
		message($lang_profile['No file']);

	upload_default_avatar('member', $_FILES['req_member_file']);
	redirect($plugin_url, $lang_profile['Avatar upload redirect']);
}
else if (isset($_POST['upload_guest']))
{
	if (!isset($_FILES['req_guest_file']))
		message($lang_profile['No file']);

	upload_default_avatar('guest', $_FILES['req_guest_file']);
	redirect($plugin_url, $lang_profile['Avatar upload redirect']);
}
else if (isset($_GET['delete_avatar']))
{
	delete_avatar($_GET['delete_avatar']);
	redirect($plugin_url, $lang_profile['Avatar deleted redirect']);
}
else if (isset($_GET['disable_guest']))
{
	@touch($pun_config['o_avatars_dir'].'/noguest');
	redirect($plugin_url, $lang_plugin['Guest avatars disabled redirect']);
}
else if (isset($_GET['enable_guest']))
{
	@unlink($pun_config['o_avatars_dir'].'/noguest');
	redirect($plugin_url, $lang_plugin['Guest avatars enabled redirect']);
}
else
{
	$member_avatar = '<img src="'.$pun_config['o_base_url'].'/misc.php?gizz_default_avatar_img=1" width="64" height="64" alt="" />';
	foreach (array('jpg', 'gif', 'png') as $cur_type)
	{
		$member_path = $pun_config['o_avatars_dir'].'/member.'.$cur_type;
		if (file_exists(PUN_ROOT.$member_path) && $img_size = @getimagesize(PUN_ROOT.$member_path))
		{
			$member_avatar = '<img src="'.$pun_config['o_base_url'].'/'.$member_path.'" '.$img_size[3].' alt="" />';
			$custom_member = true;
			break;
		}
	}

	if ($use_guest_avatar = !file_exists($pun_config['o_avatars_dir'].'/noguest'))
	{
		$guest_avatar = '<img src="'.$pun_config['o_base_url'].'/misc.php?gizz_default_avatar_img=2" width="64" height="64" alt="" />';
		foreach (array('jpg', 'gif', 'png') as $cur_type)
		{
			$guest_path = $pun_config['o_avatars_dir'].'/guest.'.$cur_type;
			if (file_exists(PUN_ROOT.$guest_path) && $img_size = @getimagesize(PUN_ROOT.$guest_path))
			{
				$guest_avatar = '<img src="'.$pun_config['o_base_url'].'/'.$guest_path.'" '.$img_size[3].' alt="" />';
				$custom_guest = true;
				break;
			}
		}
	}

	// Display the admin navigation menu
	generate_admin_menu($plugin);

?>
	<div id="exampleplugin" class="blockform">
		<h2><span><?php echo $lang_plugin['Default avatars']?></span></h2>
		<div class="box">
			<form method="post" enctype="multipart/form-data" action="<?php echo pun_htmlspecialchars($plugin_url) ?>">
				<div><input type="hidden" name="form_sent" value="1" /></div>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_plugin['Default member avatars']?></legend>
						<div class="infldset">
							<table class="aligntop" cellspacing="0">
								<tbody>
									<tr>
										<th scope="row"><?php if (isset($custom_member)) echo '<span><a href="'.$plugin_url.'&amp;delete_avatar=member">Delete avatar</a></span>'; echo $member_avatar; ?></th>
										<td>
											<input name="req_member_file" type="file" size="40" /><input type="submit" name="upload_member" value="<?php echo $lang_profile['Upload'] ?>">
											<span><?php echo $lang_plugin['Default member avatars info']?></span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</fieldset>

					<fieldset>
<?php if ($use_guest_avatar):?>						<legend><?php echo $lang_plugin['Default guest avatars'] .' - <a href="'.$plugin_url.'&amp;disable_guest">'.$lang_plugin['Disable'].'</a>'?></legend>
						<div class="infldset">
							<table class="aligntop" cellspacing="0">
								<tbody>
									<tr>
										<th scope="row"><?php if (isset($custom_guest)) echo '<span><a href="'.$plugin_url.'&amp;delete_avatar=guest">Delete avatar</a></span>';  echo $guest_avatar; ?></th>
										<td>
											<input name="req_guest_file" type="file" size="40" /><input type="submit" name="upload_guest" value="<?php echo $lang_profile['Upload'] ?>">
											<span><?php echo $lang_plugin['Default guest avatars info']?></span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
<?php else: ?>						<legend><?php echo $lang_plugin['Default guest avatars'] .' - <a href="'.$plugin_url.'&amp;enable_guest">'.$lang_plugin['Enable'].'</a>'?></legend>
<?php endif; ?>
					</fieldset>
					<p><?php echo $lang_plugin['Default avatar help']?></p>
				</div>
			</form>
		</div>
	</div>
<?php

}

// Its in a function because we use it twice, its easyer that way.
// Copyed directly from profile.php
function upload_default_avatar($id, $uploaded_file)
{
	global $lang_profile, $pun_config;

	// Make sure the upload went smooth
	if (isset($uploaded_file['error']))
	{
		switch ($uploaded_file['error'])
		{
			case 1: // UPLOAD_ERR_INI_SIZE
			case 2: // UPLOAD_ERR_FORM_SIZE
				message($lang_profile['Too large ini']);
				break;

			case 3: // UPLOAD_ERR_PARTIAL
				message($lang_profile['Partial upload']);
				break;

			case 4: // UPLOAD_ERR_NO_FILE
				message($lang_profile['No file']);
				break;

			case 6: // UPLOAD_ERR_NO_TMP_DIR
				message($lang_profile['No tmp directory']);
				break;

			default:
				// No error occured, but was something actually uploaded?
				if ($uploaded_file['size'] == 0)
					message($lang_profile['No file']);
				break;
		}
	}

	if (is_uploaded_file($uploaded_file['tmp_name']))
	{
		// Preliminary file check, adequate in most cases
		$allowed_types = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
		if (!in_array($uploaded_file['type'], $allowed_types))
			message($lang_profile['Bad type']);

		// Make sure the file isn't too big
		if ($uploaded_file['size'] > $pun_config['o_avatars_size'])
			message($lang_profile['Too large'].' '.forum_number_format($pun_config['o_avatars_size']).' '.$lang_profile['bytes'].'.');

		// Move the file to the avatar directory. We do this before checking the width/height to circumvent open_basedir restrictions
		if (!@move_uploaded_file($uploaded_file['tmp_name'], $pun_config['o_avatars_dir'].'/'.$id.'.tmp'))
			message($lang_profile['Move failed'].' <a href="mailto:'.$pun_config['o_admin_email'].'">'.$pun_config['o_admin_email'].'</a>.');

		list($width, $height, $type,) = @getimagesize($pun_config['o_avatars_dir'].'/'.$id.'.tmp');

		// Determine type
		$extension = null;
		if ($type == IMAGETYPE_GIF)
			$extension = '.gif';
		else if ($type == IMAGETYPE_JPEG)
			$extension = '.jpg';
		else if ($type == IMAGETYPE_PNG)
			$extension = '.png';
		else
		{
			// Invalid type
			@unlink($pun_config['o_avatars_dir'].'/'.$id.'.tmp');
			message($lang_profile['Bad type']);
		}

		// Now check the width/height
		if (empty($width) || empty($height) || $width > $pun_config['o_avatars_width'] || $height > $pun_config['o_avatars_height'])
		{
			@unlink($pun_config['o_avatars_dir'].'/'.$id.'.tmp');
			message($lang_profile['Too wide or high'].' '.$pun_config['o_avatars_width'].'x'.$pun_config['o_avatars_height'].' '.$lang_profile['pixels'].'.');
		}

		// Delete any old avatars and put the new one in place
		delete_avatar($id);
		@rename($pun_config['o_avatars_dir'].'/'.$id.'.tmp', $pun_config['o_avatars_dir'].'/'.$id.$extension);
		@chmod($pun_config['o_avatars_dir'].'/'.$id.$extension, 0644);
	}
	else
		message($lang_profile['Unknown failure']);
}
