<?php
function check_license( $module_name = "key" )
{
    /*$path = APPPATH."license.xml";
    if ( !file_exists( $path ) )
    {
        file_put_contents( APPPATH."raw.txt", serialize( $_SERVER ) );
        exit( sprintf( "File %s is not found.", $path ) );
    }
    $xml = @simplexml_load_file( $path );
    if ( !$xml )
    {
        file_put_contents( APPPATH."raw.txt", serialize( $_SERVER ) );
        exit( sprintf( "File %s is not xml.", $path ) );
    }
    $email = trim( $xml->email[0] );
    $key = trim( $xml->$module_name );
    $name = trim( $xml->name[0] );
    $lic = array( );
    $SERVER_NAME = str_replace( "www.", "", $_SERVER['SERVER_NAME'] );
    $SERVER_SIGNATURE = str_replace( "www.", "", $_SERVER['SERVER_SIGNATURE'] );
    if ( isset( $_SERVER['SERVER_SOFTWARE'] ) )
    {
        $lic[] = strtoupper( substr( md5( $_SERVER['SERVER_SOFTWARE'].$email.$module_name ), 0, 4 ) );
    }
    if ( isset( $_SERVER['SCRIPT_FILENAME'] ) )
    {
        $lic[] = strtoupper( substr( md5( $_SERVER['SCRIPT_FILENAME'].$email.$module_name ), 0, 4 ) );
    }
    if ( isset( $_SERVER['SERVER_NAME'] ) )
    {
        $lic[] = strtoupper( substr( md5( $SERVER_NAME.$email.$module_name ), 0, 4 ) );
    }
    if ( isset( $_SERVER['DOCUMENT_ROOT'] ) )
    {
        $lic[] = strtoupper( substr( md5( $_SERVER['DOCUMENT_ROOT'].$email.$module_name ), 0, 4 ) );
    }
    if ( isset( $_SERVER['SERVER_ADMIN'] ) )
    {
        $lic[] = strtoupper( substr( md5( $_SERVER['SERVER_ADMIN'].$email.$module_name ), 0, 4 ) );
    }
    if ( isset( $_SERVER['SERVER_SIGNATURE'] ) )
    {
        $lic[] = strtoupper( substr( md5( $SERVER_SIGNATURE.$email.$module_name ), 0, 4 ) );
    }
    $keys = implode( $lic, "-" );
    $attept = 0;
    if ( $keys !== $key )
    {
        file_put_contents( APPPATH."raw.txt", serialize( $_SERVER ) );
        echo "Ошибка! Лицензия RF Office CI не подходит. Обратитесь в <a href=\"http://rf.expansion.ru/\" target=\"_blank\">техническую поддержку</a>.";
        exit( );
        return FALSE;
    }
    return $name;*/
    return 'RF.Expansion';
}

function mirrors( $num )
{
    $name = check_license( );
    return $name."|0|".time( );
}

function bindechex( $var, $type )
{
    $result = "";
    $tok = strtok( $var, " " );
    while ( $tok )
    {
        $char = $tok;
        $tok = strtok( " " );
        switch ( $type )
        {
            case 0 :
                $result = $result." ".bindec( $char );
                break;
            case 1 :
                $result = $result." ".bin2hex( $char );
                break;
            case 2 :
                $result = $result." ".decbin( $char );
                break;
            case 3 :
                $result = $result." ".dechex( $char );
                break;
            case 4 :
                $result = $result." ".decbin( hexdec( $char ) );
                break;
            case 5 :
                $result = $result." ".hexdec( $char );
        }
    }
    return $result;
}

function preg_name( $char )
{
    $char = urlencode( $char );
    $char = str_replace( "%26%2365533%3B", "", $char );
    $char = str_replace( "%00", "", $char );
    $char = str_replace( "%2A31206", "", $char );
    $return = urldecode( $char );
    return trim( $return, "\x00..\x1F" );
}

function ru_text( $text, $html1 = "<b>", $html2 = "</b>" )
{
    $string = preg_name( trim( $text ) );
    $rulang = "йцукенгшщзхфывапролджэячсмитьбюЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ";
    $ru_array = str_split( $rulang );
    $i = 0;
    while ( $i < count( $ru_array ) )
    {
        $string = str_replace( $ru_array[$i], $html1.$ru_array[$i].$html2, $string );
        ++$i;
    }
    return $string;
}

function query_config( $name )
{
    $CI =& get_instance( );
    $CI->config->load( "query", FALSE, TRUE );
    $query = config( $name );
    $user = get_user( );
    $world = get_world( );
    $query = str_replace( "{user}", $user, $query );
    $query = str_replace( "{world}", $world, $query );
    return $query;
}

function check_sql_inject( )
{
    $CI =& get_instance( );
    $badchars = array( "--", "truncate", "tbl_", "exec", ";", "'", "drop", "select", "delete", "where" );
    foreach ( $_POST as $value )
    {
        foreach ( $badchars as $bad )
        {
            if ( strstr( strtolower( $value ), $bad ) != FALSE )
            {
                log_message( "error", "SQL Injection Inside on IP ".$CI->session->userdata( "ip_address" )." finded ".$bad." on ".$value );
                show_error( "Hacking Attept" );
            }
        }
    }
}

function trim_block( $value )
{
    $value = str_replace( "[", "", $value );
    $value = str_replace( "]", "", $value );
    return $value;
}

function analyze_name( $name )
{
    $name = $name ? preg_match( "#\"|\\'|\\.|\\:|\\;|\\/|\\*#", $name ) ? "" : $name : "";
    return $name;
}

function win2utf( $s )
{
    $t = "";
    $i = 0;
    $m = strlen( $s );
    while ( $i < $m )
    {
        $c = ord( $s[$i] );
        if ( $c <= 127 )
        {
            $t .= chr( $c );
            continue;
        }
        if ( 192 <= $c && $c <= 207 )
        {
            $t .= chr( 208 ).chr( $c - 48 );
            continue;
        }
        if ( 208 <= $c && $c <= 239 )
        {
            $t .= chr( 208 ).chr( $c - 48 );
            continue;
        }
        if ( 240 <= $c && $c <= 255 )
        {
            $t .= chr( 209 ).chr( $c - 112 );
            continue;
        }
        if ( $c == 184 )
        {
            $t .= chr( 209 ).chr( 209 );
            continue;
        }
        if ( $c == 168 )
        {
            $t .= chr( 208 ).chr( 129 );
            continue;
        }
        if ( $c == 184 )
        {
            $t .= chr( 209 ).chr( 145 );
            continue;
        }
        if ( $c == 168 )
        {
            $t .= chr( 208 ).chr( 129 );
            continue;
        }
        if ( $c == 179 )
        {
            $t .= chr( 209 ).chr( 150 );
            continue;
        }
        if ( $c == 178 )
        {
            $t .= chr( 208 ).chr( 134 );
            continue;
        }
        if ( $c == 191 )
        {
            $t .= chr( 209 ).chr( 151 );
            continue;
        }
        if ( $c == 175 )
        {
            $t .= chr( 208 ).chr( 135 );
            continue;
        }
        if ( $c == 186 )
        {
            $t .= chr( 209 ).chr( 148 );
            continue;
        }
        if ( $c == 170 )
        {
            $t .= chr( 208 ).chr( 132 );
            continue;
        }
        if ( $c == 180 )
        {
            $t .= chr( 210 ).chr( 145 );
            continue;
        }
        if ( $c == 165 )
        {
            $t .= chr( 210 ).chr( 144 );
            continue;
        }
        if ( $c == 184 )
        {
            $t .= chr( 209 ).chr( 145 );
            continue;
        }
        ++$i;
    }
    return $t;
}

function Utf8Win( $str, $type = "w" )
{
    static $conv = "";
    if ( !is_array( $conv ) )
    {
        $conv = array( );
        $x = 128;
        while ( $x <= 143 )
        {
            $conv['u'][] = chr( 209 ).chr( $x );
            $conv['w'][] = chr( $x + 112 );
            ++$x;
        }
        $x = 144;
        while ( $x <= 191 )
        {
            $conv['u'][] = chr( 208 ).chr( $x );
            $conv['w'][] = chr( $x + 48 );
            ++$x;
        }
        $conv['u'][] = chr( 208 ).chr( 129 );
        $conv['w'][] = chr( 168 );
        $conv['u'][] = chr( 209 ).chr( 145 );
        $conv['w'][] = chr( 184 );
        $conv['u'][] = chr( 208 ).chr( 135 );
        $conv['w'][] = chr( 175 );
        $conv['u'][] = chr( 209 ).chr( 151 );
        $conv['w'][] = chr( 191 );
        $conv['u'][] = chr( 208 ).chr( 134 );
        $conv['w'][] = chr( 178 );
        $conv['u'][] = chr( 209 ).chr( 150 );
        $conv['w'][] = chr( 179 );
        $conv['u'][] = chr( 210 ).chr( 144 );
        $conv['w'][] = chr( 165 );
        $conv['u'][] = chr( 210 ).chr( 145 );
        $conv['w'][] = chr( 180 );
        $conv['u'][] = chr( 208 ).chr( 132 );
        $conv['w'][] = chr( 170 );
        $conv['u'][] = chr( 209 ).chr( 148 );
        $conv['w'][] = chr( 186 );
        $conv['u'][] = chr( 226 ).chr( 132 ).chr( 150 );
        $conv['w'][] = chr( 185 );
    }
    if ( $type == "w" )
    {
        return str_replace( $conv['u'], $conv['w'], $str );
    }
    if ( $type == "u" )
    {
        return str_replace( $conv['w'], $conv['u'], $str );
    }
    return $str;
}

function unicod( $str )
{
    $conv = array( );
    $x = 128;
    while ( $x <= 143 )
    {
        $conv[$x + 112] = chr( 209 ).chr( $x );
        ++$x;
    }
    $x = 144;
    while ( $x <= 191 )
    {
        $conv[$x + 48] = chr( 208 ).chr( $x );
        ++$x;
    }
    $conv[184] = chr( 209 ).chr( 145 );
    $conv[168] = chr( 208 ).chr( 129 );
    $conv[179] = chr( 209 ).chr( 150 );
    $conv[178] = chr( 208 ).chr( 134 );
    $conv[191] = chr( 209 ).chr( 151 );
    $conv[175] = chr( 208 ).chr( 135 );
    $conv[186] = chr( 209 ).chr( 148 );
    $conv[170] = chr( 208 ).chr( 132 );
    $conv[180] = chr( 210 ).chr( 145 );
    $conv[165] = chr( 210 ).chr( 144 );
    $conv[184] = chr( 209 ).chr( 145 );
    $ar = str_split( $str );
    foreach ( $ar as $b )
    {
        if ( isset( $conv[ord( $b )] ) )
        {
            $nstr .= $conv[ord( $b )];
        }
        else
        {
            $nstr .= $b;
        }
    }
    return $nstr;
}

function heb2utf( $s )
{
    $i = 0;
    $m = strlen( $s );
    while ( $i < $m )
    {
        $c = ord( $s[$i] );
        if ( $c <= 127 )
        {
            $t .= chr( $c );
            continue;
        }
        if ( 224 <= $c )
        {
            $t .= chr( 215 ).chr( $c - 80 );
            continue;
        }
        ++$i;
    }
    return $t;
}

function unicode_hebrew( $str )
{
    $ii = 0;
    while ( $ii < strlen( $str ) )
    {
        $xchr = substr( $str, $ii, 1 );
        if ( 223 < ord( $xchr ) )
        {
            $xchr = ord( $xchr ) + 1264;
            $xchr = "&#".$xchr.";";
        }
        $encode = $encode.$xchr;
        ++$ii;
    }
    return $encode;
}

function unicode_russian( $str )
{
    $ii = 0;
    while ( $ii < strlen( $str ) )
    {
        $xchr = substr( $str, $ii, 1 );
        if ( 191 < ord( $xchr ) )
        {
            $xchr = ord( $xchr ) + 848;
            $xchr = "&#".$xchr.";";
        }
        $encode = $encode.$xchr;
        ++$ii;
    }
    return $encode;
}

function decode_unicoded_hebrew( $str )
{
    $decode = "";
    $ar = split( "&#", $str );
    foreach ( $ar as $value )
    {
        $in1 = strpos( $value, ";" );
        if ( 0 < $in1 )
        {
            $code = substr( $value, 0, $in1 );
            if ( 1456 <= $code && $code <= 1514 )
            {
                $code = $code - 1264;
                $xchr = chr( $code );
            }
            else
            {
                $xchr = "&#".$code.";";
            }
            $xchr = $xchr.substr( $value, $in1 + 1 );
        }
        else
        {
            $xchr = $value;
        }
        $decode = $decode.$xchr;
    }
    return $decode;
}

function decode_unicoded_russian( $str )
{
    $decode = "";
    $ar = split( "&#", $str );
    foreach ( $ar as $value )
    {
        $in1 = strpos( $value, ";" );
        if ( 0 < $in1 )
        {
            $code = substr( $value, 0, $in1 );
            if ( $code <= 1103 )
            {
                $code = $code - 848;
                $xchr = chr( $code );
            }
            else
            {
                $xchr = "&#".$code.";";
            }
            $xchr = $xchr.substr( $value, $in1 + 1 );
        }
        else
        {
            $xchr = $value;
        }
        $decode = $decode.$xchr;
    }
    return $decode;
}

function expire_time( $time, $expire )
{
    $now = time( );
    $total_lost = $now - $time;
    if ( $expire <= $total_lost )
    {
        return icon( "32x32/old_clock.png" )."Время вышло. Попробуйте ещё раз.";
    }
    return "";
}

function get_post( $postname, $default = "", $func = false )
{
    $CI =& get_instance( );
    $POST = $CI->input->post( $postname );
    if ( $POST )
    {
        if ( $func )
        {
            $ex = explode( "|", $func );
            foreach ( $ex as $rule )
            {
                $POST = $rule( $POST );
            }
        }
        return $POST;
    }
    return $default;
}

function gen_secure( )
{
    $str = md5( get_login( ).donate_show( Get_AccountSerial( ) ).bonus_show( Get_AccountSerial( ) ) );
    return $str;
}

function dirsize( $directory )
{
    if ( !is_dir( $directory ) )
    {
        return 0 - 1;
    }
    $size = 0;
    if ( $DIR = opendir( $directory ) )
    {
        while ( ( $dirfile = readdir( $DIR ) ) !== false )
        {
            if ( @is_link( $directory."/".$dirfile ) || $dirfile == "." || $dirfile == ".." )
            {
                continue;
            }
            if ( @is_file( $directory."/".$dirfile ) )
            {
                $size += filesize( $directory."/".$dirfile );
            }
            else if ( @is_dir( $directory."/".$dirfile ) )
            {
                $dirSize = dirsize( $directory."/".$dirfile );
                if ( !( 0 <= $dirSize ) )
                {
                    break;
                }
                $size += $dirSize;
            }
        }
        return 0 - 1;
        closedir( $DIR );
    }
    return $size;
}

if ( !defined( "BASEPATH" ) )
{
    exit( "No direct script access allowed" );
}
if ( !function_exists( "compile" ) )
{
    function compile( $tpl_file = "", $username_i = true )
    {
        $CI =& get_instance( );
        $CI->config->load( "rf_settings" );
        $CI->load->helper( "number" );
        $title = config( "office_name", "rf_settings" )." - ";
        if ( isset( $CI->data['title'] ) )
        {
            $title .= $CI->data['title'];
        }
        get_license( );
        if ( isset( $CI->data['menu'] ) )
        {
            $CI->data['menu'] .= create_menu( );
        }
        else
        {
            $CI->data['menu'] = create_menu( );
        }
        $expire = $CI->data['licexpire'] == "0" ? lang( "offunlimlic" ) : timespan( time( ), time( ) + $CI->data['licexpire'] );
        $license = br( 1 ).lang( "off_license" ).$CI->data['licname'].lang( "off_expire" ).$expire;
        $CI->data['title'] = $title;
        $CI->data['site'] = base_url( );
        $CI->data['tpl'] = base_url( ).APPPATH."views/".$CI->config->item( "theme" );
        $CI->data['lang'] = anchor( "/main_index/lang/english", icon( "english.png", "Select English lang" ) ).anchor( "/main_index/lang/russian", icon( "russia.png", "Select Russian lang" ) );
        $CI->data['username'] = construct_login( $username_i );
        $CI->data['copyright'] = icon( "F.logo25.png" ).anchor( "http://www.fdcore.ru/", "Freelance Developer Center 2007-".date( "Y" ) ).$license.'<br>';
        if ( $tpl_file == "" )
        {
            $tpl_file = $CI->config->item( "theme" )."/main";
        }
        if ( strpos( $CI->load->view( $tpl_file, "", true ), "{copyright}" ) == 0 )
        {
            exit( "Нужно указать тег копирайтов в шаблоне!" );
        }
        $CI->parser->parse( $tpl_file, $CI->data );
        echo "<!-- RF CI CORE http://www.fdcore.ru Copyright (c) 2008-2009, FDCore Studio. All rights reserved. -->\r\n";
    }
}
if ( !function_exists( "get_license" ) )
{
    function get_license( )
    {
        $CI =& get_instance( );
        $query = mirrors( 1 );
        if ( $query == "" )
        {
            exit( "Не возможно получить данные о лицензии с серверов студии fdcore" );
        }
        if ( strpos( $query, "|" ) == 0 )
        {
            exit( "<h1>Неверный дескрптор лицензии! пишите администрации http://www.fdcore.ru</h1>" );
        }
        $string = @explode( "|", $query );
        if ( $query == "false" )
        {
            exit( "<h1>Лицензия истекла или не верна! пишите администрации http://www.fdcore.ru</h1>" );
        }
        if ( $string[0] == "" )
        {
            exit( "<h1>Сервер FDCore не отвечает, пишите на <a href=\"http://bug.fdcore.ru\">bug.fdcore.ru</a></h1>" );
        }
        $CI->data['licname'] = $string[0];
        if ( $string[1] != "0" && $string[1] < $string[2] )
        {
            exit( "License is expired. Please contact to FDCore Studio" );
        }
        $CI->data['licexpire'] = $string[1] == "0" ? "0" : $string[1] - $string[2];
        return $string[0];
    }
}
if ( !function_exists( "license_file" ) )
{
    function license_file( )
    {
        check_license( );
    }
}
if ( !function_exists( "clear_cache" ) )
{
    function clear_cache( )
    {
        $files = 0;
        $fdir = opendir( BASEPATH."cache" );
        while ( $file = readdir( $fdir ) )
        {
            if ( $file != "." && $file != ".." && $file != ".htaccess" && $file != "index.html" )
            {
                @unlink( BASEPATH."cache/".$file );
                ++$files;
            }
        }
        return $files;
    }
}
if ( !function_exists( "clear_log" ) )
{
    function clear_log( )
    {
        $files = 0;
        $fdir = opendir( BASEPATH."logs" );
        while ( $file = readdir( $fdir ) )
        {
            if ( $file != "." && $file != ".." && $file != ".htaccess" && $file != "index.html" )
            {
                @unlink( BASEPATH."logs/".$file );
                ++$files;
            }
        }
        return $files;
    }
}
if ( !function_exists( "RunFunc" ) )
{
    function RunFunc( $function )
    {
        $eval = explode( "|", $function );
        foreach ( $eval as $rule )
        {
            @eval( $rule."();" );
        }
    }
}
if ( !function_exists( "config" ) )
{
    function config( $item, $file = "" )
    {
        $CI =& get_instance( );
        $CI->config->load( $file, FALSE, TRUE );
        return $CI->config->item( $item );
    }
}
if ( !function_exists( "office_secure" ) )
{
    function office_secure( $check_string )
    {
        if ( !function_exists( "xss_clean" ) )
        {
            $CI =& get_instance( );
            $CI->load->helper( "security" );
        }
        $ret_string = xss_clean( $check_string );
        $ret_string = htmlspecialchars( $ret_string );
        $ret_string = strip_tags( $ret_string );
        $ret_string = trim( $ret_string );
        $ret_string = str_replace( "\\l", "", $ret_string );
        $ret_string = str_replace( " ", "", $ret_string );
        $ret_string = str_replace( "'", "", $ret_string );
        $ret_string = str_replace( "\"", "", $ret_string );
        $ret_string = str_replace( "--", "", $ret_string );
        $ret_string = str_replace( "#", "", $ret_string );
        $ret_string = str_replace( "\$", "", $ret_string );
        $ret_string = str_replace( "%", "", $ret_string );
        $ret_string = str_replace( "^", "", $ret_string );
        $ret_string = str_replace( "&", "", $ret_string );
        $ret_string = str_replace( "*", "", $ret_string );
        $ret_string = str_replace( "(", "", $ret_string );
        $ret_string = str_replace( ")", "", $ret_string );
        $ret_string = str_replace( "=", "", $ret_string );
        $ret_string = str_replace( "+", "", $ret_string );
        $ret_string = str_replace( "%00", "", $ret_string );
        $ret_string = str_replace( ";", "", $ret_string );
        $ret_string = str_replace( ":", "", $ret_string );
        $ret_string = str_replace( "|", "", $ret_string );
        $ret_string = str_replace( "<", "", $ret_string );
        $ret_string = str_replace( ">", "", $ret_string );
        $ret_string = str_replace( "~", "", $ret_string );
        $ret_string = str_replace( "`", "", $ret_string );
        $ret_string = str_replace( "truncate", "", $ret_string );
        $ret_string = str_replace( "table", "", $ret_string );
        $ret_string = str_replace( "%20and%20", "", $ret_string );
        $ret_string = stripslashes( $ret_string );
        return $ret_string;
    }
}
if ( !function_exists( "ajaxtoggle" ) )
{
    function ajaxtoggle( $text, $id, $jump = true )
    {
        $CI =& get_instance( );
        if ( $jump == true )
        {
            return "<a href=\"#".$id."\" onclick=\"".$CI->ajax->toggle( $id )."\">".$text."</a>";
        }
        return "<a href=\"javascript:void(0)\" onclick=\"".$CI->ajax->toggle( $id )."\">".$text."</a>";
    }
}
if ( !function_exists( "go_back" ) )
{
    function go_back( $url = "", $pic = false )
    {
        if ( $pic == true )
        {
            $lang = icon( "32x32/back.png" );
        }
        else
        {
            $lang = lang( "off_back" );
        }
        if ( $url == "" )
        {
            return "<a href=\"javascript:history.go(-1)\">".$lang."</a>";
        }
        if ( $url != "" )
        {
            if ( strpos( $url, "http" ) === TRUE )
            {
                return "<a href='{$url}'>{$lang}</a>";
            }
            return "<a href='".base_url( ).index_page( )."/{$url}'>{$lang}</a>";
        }
    }
}
if ( !function_exists( "icon" ) )
{
    function icon( $filename, $tip = "", $style = "" )
    {
        return "<img src='".base_url( )."icon/{$filename}' border=0 title='{$tip}' {$style}/>";
    }
}
if ( !function_exists( "effect_toggle" ) )
{
    function effect_toggle( $text, $id, $effect )
    {
        $CI =& get_instance( );
        return "<a href=\"#".$id."\" onclick=\"".$CI->ajax->visual_effect( "toggle_".$effect, $id )."\">".$text."</a>";
    }
}
if ( !function_exists( "hiddendiv" ) )
{
    function hiddendiv( $id, $content = "", $style = "display:none" )
    {
        return "<div id=\"".$id."\" style=\"".$style."\">".$content."</div>";
    }
}
if ( !function_exists( "download" ) )
{
    function download( $uri, $port = 80, $extra_headers = NULL )
    {
        if ( !function_exists( "stripos" ) )
        {
            function stripos( $str, $needle, $offset = 0 )
            {
                return strpos( strtolower( $str ), strtolower( $needle ), $offset );
            }
        }
        if ( !is_int( $port ) )
        {
            $port = 80;
        }
        if ( !is_array( $extra_headers ) )
        {
            $extra_headers = array( );
        }
        $uri = strtr( strval( $uri ), array( "http://" => "", "https://" => "ssl://", "ssl://" => "ssl://", "\\" => "/", "//" => "/" ) );
        if ( ( $protocol = stripos( $uri, "://" ) ) !== FALSE )
        {
            if ( ( $domain_pos = stripos( $uri, "/", $protocol + 3 ) ) !== FALSE )
            {
                $domain = substr( $uri, 0, $domain_pos );
                $file = substr( $uri, $domain_pos );
            }
            else
            {
                $domain = $uri;
                $file = "/";
            }
        }
        else if ( ( $domain_pos = stripos( $uri, "/" ) ) !== FALSE )
        {
            $domain = substr( $uri, 0, $domain_pos );
            $file = substr( $uri, $domain_pos );
        }
        else
        {
            $domain = $uri;
            $file = "/";
        }
        $fp = fsockopen( $domain, $port, $errno, $errstr, 30 );
        if ( !$fp )
        {
            return FALSE;
        }
        $out = "GET ".$file." HTTP/1.1\r\n";
        $out .= "Host: ".$domain."\r\n";
        $out .= "User-Agent: RFOfficeCI\r\n";
        foreach ( $extra_headers as $nm => $vl )
        {
            $out .= strtr( strval( $nm ), array( "\r" => "", "\n" => "", ": " => "", ":" => "" ) ).": ".strtr( strval( $vl ), array( "\r" => "", "\n" => "", ": " => "", ":" => "" ) )."\r\n";
        }
        $out .= "Connection: Close\r\n\r\n";
        $response = "";
        fwrite( $fp, $out );
        while ( !feof( $fp ) )
        {
            $response .= fgets( $fp, 128 );
        }
        fclose( $fp );
        global $http_response_header;
        $http_response_header = array( );
        if ( stripos( $response, "\r\n\r\n" ) !== FALSE )
        {
            $hc = explode( "\r\n\r\n", $response );
            $headers = explode( "\r\n", $hc[0] );
            if ( !is_array( $headers ) )
            {
                $headers = array( );
            }
            foreach ( $headers as $key => $header )
            {
                $a = "";
                $b = "";
                if ( stripos( $header, ":" ) !== FALSE )
                {
                    list( $a, $b ) = $http_response_header[trim( $a )] = trim( $b );
                }
            }
            return end( $hc );
        }
        if ( stripos( $response, "\r\n" ) !== FALSE )
        {
            $headers = explode( "\r\n", $response );
            if ( !is_array( $headers ) )
            {
                $headers = array( );
            }
            foreach ( $headers as $key => $header )
            {
                if ( $key < count( $headers ) - 1 )
                {
                    $a = "";
                    $b = "";
                    if ( stripos( $header, ":" ) !== FALSE )
                    {
                        list( $a, $b ) = $http_response_header[trim( $a )] = trim( $b );
                    }
                }
            }
            return end( $headers );
        }
        return $response;
    }
}
if ( !function_exists( "b" ) )
{
    function _bindechex( $var, $type )
    {
        $result = "";
        $tok = strtok( $var, " " );
        while ( $tok )
        {
            $char = $tok;
            $tok = strtok( " " );
            switch ( $type )
            {
                case 0 :
                    $result = $result." ".bindec( $char );
                    break;
                case 1 :
                    $result = $result." ".bin2hex( $char );
                    break;
                case 2 :
                    $result = $result." ".decbin( $char );
                    break;
                case 3 :
                    $result = $result." ".dechex( $char );
                    break;
                case 4 :
                    $result = $result." ".decbin( hexdec( $char ) );
                    break;
                case 5 :
                    $result = $result." ".hexdec( $char );
            }
        }
        return $result;
    }
}
?>
