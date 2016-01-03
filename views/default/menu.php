<?php
//START
$CI =& get_instance();
$CI->config->load('rf_settings');
$menu='';
        // если пользователь админ
	if(is_adm_logged())
	{
		$menu.='<li>'.anchor('admin',lang('off_main')).'</li>'
		.'<li>'.anchor('admin/category',lang('off_cat')).'</li>'
		.'<li>'.anchor('admin/addcategory',lang('off_addcat')).'</li>'
		.'<li>'.anchor('admin/items',lang('off_items')).'</li>'
		.'<li>'.anchor('admin/additems',lang('off_additems')).'</li>'
		.'<li>'.anchor('admin/setup',lang('off_settings')).'</li>'
		.'<li>'.anchor('admin/account',lang('off_account')).'</li>'
		.'<li>'.anchor('admin/tools','Деньги').'</li>'
  		.'<li>'.anchor('history/','История').'</li>';
 
        //инклудим доп ссылки	
	if (file_exists(APPPATH."views/admin_menu.php")) {
		@include (APPPATH."views/admin_menu.php");
	}
}
// если пользователь гм
if(is_gm())
{
    $menu.=$CI->parser->parse('gm_menu',array('li_menu'=>$menu),true);
}
// если простой пользователь (авторизованный)
if(is_logged())$menu.='<li>'.anchor('main',lang('off_main')).'</li>';    
if(is_logged())$menu.='<li>'.anchor('main/account',lang('off_menu_acci')).'</li>';
if(is_logged())$menu.='<li>'.anchor('shop',lang('off_menu_shop')).'</li>';
if(is_logged())$menu.='<li>'.anchor('main/characters',lang('off_menu_chari')).'</li>';
//if(is_logged())$menu.='<li>'.anchor('vote',lang('off_vote')).'</li>';	    
if(config('module_vote','core')==true)if(is_logged())$menu.='<li>'.anchor('vote',lang('off_vote')).'</li>';
if(config('module_mmotop','core')==true)if(is_logged())$menu.='<li>'.anchor('mmotop','Голосовать на mmotop').'</li>';	    
if(is_logged())$menu.='<li>'.anchor('main/profile',lang('off_profile_menu')).'</li>';
if(is_logged() || is_gm() || is_adm_logged())$menu.='<li>'.anchor('main/logout',lang('off_logout')).'</li>';
 
echo $menu;
 
//END