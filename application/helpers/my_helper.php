<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function form_url($target = NULL, $attr = array('class' =>'form-horizontal'))
{
	echo form_open(NULL, $attr);
}

function btn_link($uri = 'site', $title = 'Button', $attr = NULL)
{
    echo anchor(site_url($uri), $title, $attr);
}

function btn_add($uri = 'site', $title = 'Add New')
{
    echo anchor($uri, '<i class="fa fa-plus"></i>' . $title, array(
														        'class'=>'btn btn-primary btn_add',
														    ));
}

function btn_edit($uri = 'site', $title = 'Edit')
{
    echo anchor($uri, '<i class="fa fa-edit"></i>' . $title, array(
														        'class'=>'btn btn-info btn-sm btn_edit',
														    ));
}

function btn_delete($uri = 'site', $title = 'Delete')
{
    echo anchor($uri, '<i class="fa fa-trash"></i>' . $title, array(
                                                                'class'=>'btn btn-danger btn-sm btn_delete',
														        'data-target'=>'.show_modal',
                                                                'data-toggle'=>'modal',
														    ));
}

function btn_search($name = 'btn_search', $title = 'Search')
{
    echo form_button(array(
				        'type'=>'submit',
				        'name'=>$name,
				        'content'=>'<i class="fa fa-search"></i>'.$title,
				        'class'=>'btn btn-default btn_search',
				    ));
}

function btn_clear($name = 'btn_clear', $title = 'Clear')
{
    echo form_button(array(
				        'type'=>'reset',
				        'name'=>$name,
				        'content'=>'<i class="fa fa-times"></i>'.$title,
				        'class'=>'btn btn-default',
				    ));
}

function btn_cancel($uri = 'site', $title = 'CANCEL')
{
    echo anchor($uri, '<i class="fa fa-close"></i>' . $title, array(
                                                                'class'=>'btn btn-default btn-lg btn-block btn_cancel',
                                                            ));
}

function btn_save($name = 'btn_save', $title = 'Save Record')
{
    echo form_button(array(
                        'type'=>'submit',
                        'name'=>$name,
                        'content'=>'<i class="fa fa-check"></i>'.$title,
                        'class'=>'btn btn-success btn-lg btn-block btn_save',
                    ));
}

function input_form($name = NULL, $value = NULL, $attr = NULL)
{
	$attrib = 'class="form-control" ' .$attr;
	echo form_input($name, $value, $attrib);
}

function password_form($name = NULL, $value = NULL, $attr = NULL)
{
	$attrib = 'class="form-control" ' .$attr;
	echo form_password($name, $value, $attrib);
}

function hidden_form($name = NULL, $value = NULL, $attr = NULL)
{
	echo form_hidden($name, $value, $attr);
}

function search_form($title= NULL, $options = NULL)
{
    echo form_open(NULL,'class="form-inline"');
    echo '<div class="search_form_container">';
    echo anchor(strtolower($title).'/new', '<i class="fa fa-plus"></i>Add New', array('class'=>'btn btn-primary btn_add'));
    echo '<div class="search_form">';
    echo form_dropdown('filter', $options, set_value('filter'), 'class="form-control"');
    echo form_input('search_q', NULL, 'class="form-control"');
    echo form_button(array('type'=>'submit',
                           'name'=>'btn_search',
                           'value'=>'search',
                           'content'=>'<i class="fa fa-search"></i>Search',
                           'class'=>'btn btn-default btn_search'));
    echo '</div></div>';
    echo form_close();
}

function fnum($number,$dplace)
{
    return number_format($number, $dplace, '.',',');
}

function time_to_decimal($time) {
    $timeArr = explode(':', $time);
    $decTime = ($timeArr[0]) + ($timeArr[1]/60);
    return $decTime;
}

function appname()
{
	return 'UMak-HRIS';
}

function title()
{
    $CI =& get_instance();
    return ucfirst($CI->router->class); 
}        


/**
 * Dump helper. Functions to dump variables to the screen, in a nicley formatted manner.
 * @author Joost van Veen
 * @version 1.0
 */
if (!function_exists('dump')) {
    function dump ($var, $label = 'Dump', $echo = TRUE)
    {
        // Store dump in variable 
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        
        // Add formatting
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 10px; margin: 10px 0; text-align: left;">' . $label . ' => ' . $output . '</pre>';
        
        // Output
        if ($echo == TRUE) {
            echo $output;
        }
        else {
            return $output;
        }
    }
}


if (!function_exists('dump_exit')) {
    function dump_exit($var, $label = 'Dump', $echo = TRUE) {
        dump ($var, $label, $echo);
        exit;
    }
}

/* End of file my_helper.php */
/* Location: ./application/helpers/my_helper.php */