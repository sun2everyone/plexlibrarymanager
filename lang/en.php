<?php
//Messages
$strings['error']="Error: ";
$strings['warning']="Warning: ";
$strings['unconfigured']="Before use you should adjust settings in config.php!";
$strings['no_folder_selected']="You have to select folder with video!";
$strings['title_add_success']="Title %s (%s) successfully saved to library %s.";
$strings['movie_add_success']="%s successfully saved to library %s.";
$strings['no_edit_function']="Edit functionality unwritten yet. You can delete title/season ar simpy re-add it :)";
$strings['del_success']="Deleted successfully.";
$strings['msg_season']=" season";
$strings['msg_specials']="special";
$strings['title_exists']="Library %s already has title with this name! If you continue this folder will be added as %s of %s.";
$strings['season_exists']="%s of %s is already in the library %s. If you continue it will be overwritten!";
$strings['movie_exists']="%s is already in the library %s. If you continue it will be overwritten!";

//Errors
$strings['err_folder_contents']="Error trying to get folder contents!";
$strings['err_empty_path']="Cannot get folder contents - path empty!";
$strings['err_title_data']="No title data recieved!";
$strings['err_lib_save']="Library saving failed.";
$strings['err_title_add']="Adding title to library failed.";
$strings['err_no_season']="Unable to delete - no such season.";
$strings['err_no_title']="Unable to delete - no such title.";
$strings['err_title_del_name']="Unable to delete - wrong title name.";
$strings['err_no_vid_in_dir']="No video files found in directory %s!";
$strings['err_vid_dir']="Wrong video directory!";
$strings['err_title_name_length']="Bad title name! Title name should have from 1 to 100 charactes!";
$strings['err_title_name_symbol']="Bad title name! No special symbols!";
$strings['err_select_season']="You have to select season!";
$strings['err_select_episode']="You have to select at least one episode!";
$strings['err_ep_id']="Episode number must be inique and above zero!";
$strings['err_ep_id_renum']="Incorrect episode number!";
$strings['err_file_already_added']="File already added.";

//Template
$strings['confirmation']="Are you sure? This action can't be undone!";
$strings['disclaimer']='*Symlinks used to create library. This software is distributed "as is", <br>and author is not responsible for any problems you might get using it, <font color="red">including data loss or corruption!</font>*<br >';
$strings['path_to_root_media']="Path to root folder of source media files: ";
$strings['path_to_root_library']="Path to your Plex anime library: ";
$strings['switch_library']="Switch library:";
$strings['header_add_title']="Adding title to library %s:";
$strings['header_view_lib']="Library %s contents:";
$strings['header_select_folders']="Step 1. Select which folders/files for video, subtitles and audio to use:";
$strings['settings']="Settings:";
$strings['guess_ep_numbers']="Guess episode number using filename.";
$strings['guess_ep_numbers_hint']="(Try to turn this off if you get wrong numeration).";
$strings['header_check_parsing']="Step 2. Check parsing results:";
$strings['th_vid']="Video";
$strings['th_title']="Source name";
$strings['th_sub']="Subtitles";
$strings['th_aud']="Audio";
$strings['special']="Special";
$strings['th_season']="Season number/Specials:";
$strings['th_pref_sub']="Preferred subtitles folder:";
$strings['th_pref_aud']="Preferred audio folder:";
$strings['th_ep_list']="Episodes list:";
$strings['video_folder_selected']="Selected file/video directory: ";
$strings['suggested_name']="Suggested name:";
$strings['tvdb']="Must fit TVDB/AniDB<br>Movie naming: Title (year)";
$strings['edit_field']="(Click on the field to edit)";
$strings['entry_yes']="Yes";
$strings['entry_no']="No";
$strings['err_try_another']="Error! Try different folder!";
$strings['title_seasons']="%s; Episodes: %s";
$strings['lib_empty']="Library is empty yet!";
$strings['select_file']="Select file:";
$strings['starting_number']="Enter starting number:";

//Links, hints and buttons
$strings['main_page']="Main page";
$strings['view_library']="View library";
$strings['add_title']="Add new title";
$strings['add_to_lib']="Add to library";
$strings['autoparsing']="Analyze";
$strings['ch_folder_sel']="Change folder selection";
$strings['go_top']="Page top";
$strings['change']="Change";
$strings['del']="Delete";
$strings['add']="Add";
$strings['submit']="Submit";
$strings['sort']="Sort";
$strings['renumerate']="Automatically numerate selected";