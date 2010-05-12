********************************************************************
              F L U X B B     M O D I F I C A T I O N
********************************************************************
Name:          Default Avatar
Author:        Gizzmo <justgiz@gmail.com>
Version:       1.0.2
Release date:  May 1st 2010
Works on:      1.4-rc3
********************************************************************
DISCLAIMER:
	Please note that "mods" are not officially supported by FluxBB.
	Installation of this modification is done at your own risk.
	Backup your forum database and any and all applicable files
	before proceeding.

DESCRIPTION:
	This modification adds a avatar to guests and users who do not
	currently have a avatar uploaded.

AFFECTED FILES:
	misc.php
	viewtopic.php

NOTES:
	- To use your own image as the default avatar upload the image to
	  the avatar directory with either the name 'member.png' or
	  'guest.png'. (extension must be 'jpg', 'gif', or 'png')

	- The default avatars provided with this mod are store in a
	  encode state (base64) in misc.php

	- You do not need to use the plugin to use this mod. The plugin is
	  purely optional.

TODO:
	- Add a plugin to give easyer access to set the avatars and
	  enable / disable guess avatars.

********************************************************************
INSTALLATION:

	1. Upload the following files to the root directory of your forum.
		lang/		(folder)
		plugins/	(folder)

	2. Follow the following steps to make the changes to files.

********************************************************************
#-------[ 1. Open ]

   misc.php


********************************************************************
#-------[ 2. Find (line:328) ]

else
	message($lang_common['Bad request']);


********************************************************************
#-------[ 3. Before, Add ]

// Default Avatar by Gizzmo - Start
else if (isset($_GET['gizz_default_avatar_img']) && in_array($_GET['gizz_default_avatar_img'], array(1,2)))
{
	$avatar = intval($_GET['gizz_default_avatar_img']);
	$avatars = array(
		// User
		1 => array(
			'type' => 'png',
			'size' => '4322',
			'code' => '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'
		),
		// Guest
		2 => array(
			'type' => 'png',
			'size' => '5959',
			'code' => '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'
		)
	);

	header("Content-type: image/".$avatars[$avatar]['type']);
	header("Content-length: ".$avatars[$avatar]['size']);
	exit(base64_decode($avatars[$avatar]['code']));
}
// Default Avatar by Gizzmo - END


********************************************************************
#-------[ 4. Open ]

	viewtopic.php


********************************************************************
#-------[ 5. Find (line:319) ]

			$signature = parse_signature($cur_post['signature']);
			$signature_cache[$cur_post['poster_id']] = $signature;
		}
	}


********************************************************************
#-------[ 6. After, Add ]

	// Default Avatar by Gizzmo - Start
	if ($cur_post['poster_id'] > 1)
	{
		if ($user_avatar == '' && $pun_config['o_avatars'] == '1' && $pun_user['show_avatars'] != '0')
		{
			// was the default memeber avatar previosly found
			if (!isset($default_member_avatar))
			{
				// start with using the provided avatar
				$default_member_avatar = '<img src="'.$pun_config['o_base_url'].'/misc.php?gizz_default_avatar_img=1" width="64" height="64" alt="" />';

				// then look for a uploaded avatar
				foreach (array('jpg', 'gif', 'png') as $cur_type)
				{
					$path = $pun_config['o_avatars_dir'].'/member.'.$cur_type;

					if (file_exists(PUN_ROOT.$path) && $img_size = @getimagesize(PUN_ROOT.$path))
					{
						$default_member_avatar = '<img src="'.$pun_config['o_base_url'].'/'.$path.'" '.$img_size[3].' alt="" />';
						break;
					}
				}
			}

			// Set and cache $user_avatar with the default member avatar
			$user_avatar = $user_avatar_cache[$cur_post['poster_id']] = $default_member_avatar;
		}
	}
	else
	{
		// check and cache if the 'noguest' file exists
		if (!isset($use_guest_avatar))
			$use_guest_avatar = !file_exists($pun_config['o_avatars_dir'].'/noguest');

		if ($use_guest_avatar && $pun_config['o_avatars'] == '1' && $pun_user['show_avatars'] != '0')
		{
			// was the guest avatar previosly found
			if (!isset($default_guest_avatar))
			{
				// start with using the provided avatar
				$default_guest_avatar = '<img src="'.$pun_config['o_base_url'].'/misc.php?gizz_default_avatar_img=2" width="64" height="64" alt="" />';

				// then look for a uploaded avatar
				foreach (array('jpg', 'gif', 'png') as $cur_type)
				{
					$path = $pun_config['o_avatars_dir'].'/guest.'.$cur_type;

					if (file_exists(PUN_ROOT.$path) && $img_size = @getimagesize(PUN_ROOT.$path))
					{
						$default_guest_avatar = '<img src="'.$pun_config['o_base_url'].'/'.$path.'" '.$img_size[3].' alt="" />';
						break;
					}
				}
			}

			// Set $user_avatar with the default guest avatar
			$user_avatar = $default_guest_avatar;
		}
	}
	// Default Avatar by Gizzmo - END


********************************************************************
#-------[ 7. Save and Upload! ]
