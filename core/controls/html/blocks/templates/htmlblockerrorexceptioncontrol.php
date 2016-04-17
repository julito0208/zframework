<?php
$trace_odd_item_background = '#DDD';
$trace_even_item_background = '#EEE';
$trace_item_style = "list-style-type: none; font-size: 11pt; padding: 7px 10px;";
$trace_item_file_style = "font-weight: bold; ";
$trace_item_function_style = "";
$line_number_style = "display: inline-block; width: 50px; color: #888;";
$selected_line_number_style = "color: #EEE;";
$line_style = "font-family: mono;font-size: 10pt; font-weight: bold;";
$stack_trace_file_style = "font-weight: bold;";
$stack_trace_line_style = "";
$stack_trace_line_number_style = "font-weight: bold; color: #555; padding-right: 10px; padding-left: 5px;";
$stack_trace_line_index_style = "display: inline-block; width: 30px; color: #555; ";
$current_line_style = "background: #FF7070 !important; font-weight: bold; color: #FFF !important;";
$header_row_style = "padding: 3px 0;";
$link_style= "color: #FFF; text-decoration: underline; ";
$box_style = "box-shadow: 2px 2px 1px 1px rgba(0,0,0,0.5); border: solid 1px #111; border-radius: 5px; margin-bottom: 40px !important; ";
$red_box_style = "box-shadow: 2px 2px 1px 1px rgba(0,0,0,0.5); border: solid 1px #EEE; border-radius: 5px; margin-bottom: 40px !important; ";
$list_style = "border-radius: 5px;";
$first_list_item_style = "border-radius: 5px 5px 0 0;";
$last_list_item_style = "border-radius: 0 0 5px 5px;";
$list_item_style = "border-radius: 0;";
?>

<div style="padding: 20px; background: #F2F2F2;">

	<div style="background: #F00; padding: 10px 15px; border: solid 1px #A88; font-size: 12pt; <?=$red_box_style?>">
		
		<div style="text-transform: uppercase; font-weight: bold; padding-right: 5px; color: #FFF; text-decoration: underline; padding-bottom: 8px;">
			Error
		</div>
		
		<div style="<?=$header_row_style?>">
			<span style="color: #FFF; font-weight: bold; padding-right: 5px;">
				Site: 
			</span>
			<span style="color: #FFF">
				<?=ZPHP::get_config('site_name')?>
				<span style="padding-left: 10px;">[ <a target="_blank" style="<?=$link_style?>" href="<?=ZPHP::get_site_url(true)?>"><?=ZPHP::get_site_url(true)?></a> ]</span>
			</span>
		</div>
	
		<div style="<?=$header_row_style?>">
			<span style="color: #FFF; font-weight: bold; padding-right: 5px;">
				Request URL: 
			</span>
			<span style="color: #FFF">
				<?=ZPHP::get_actual_uri()?>
				<span style="padding-left: 10px;">[ <a target="_blank" style="<?=$link_style?>" href="<?=ZPHP::get_site_url(true).ZPHP::get_actual_uri()?>"><?=ZPHP::get_site_url(true).ZPHP::get_actual_uri()?></a> ]</span>
			</span>
		</div>
	
		<div style="<?=$header_row_style?>">
			<span style="color: #FFF; font-weight: bold; ">
				Message: 
			</span>
			<span style="color: #FFF">
				<?=$message?>
			</span>
		</div>
	
		<? if($type_name): ?>
			<div style="<?=$header_row_style?>">
				<span style="color: #FFF; font-weight: bold; ">
					Error Type: 
				</span>
				<span style="color: #FFF">
					<?=$type_name?>
				</span>
			</div>
		<? endif; ?>

	</div>
	
	<? if(!empty($stack_trace)): ?>
	
		<div style="margin: 20px 0 0;<?=$box_style?>">
			<ul style="list-style-type: none; width: 100%; list-style-position: inside;margin:0;padding:0;<?=$list_style?>">
				
				<li style="margin-left: 0; padding: 10px 10px; background: <?=$trace_odd_item_background?>;background: #F00;">
					<span style="color: #DDD; <?=$trace_item_file_style?>">
						Stack Trace
					</span>
				</li>

				<? foreach($stack_trace as $index => $line): ?>
				<li style="margin-left: 0;background: <?=($index % 2 == 0 ? $trace_even_item_background : $trace_odd_item_background)?> !important;<?=$trace_item_style?><? if($line_number == $line) echo $current_line_style; ?> <?=($index == count($stack_trace)-1 ? $last_list_item_style : $list_item_style)?>">
					<span style="<?=$stack_trace_line_index_style?>">
						#<?=($index+1)?>
					</span>
					<span style="<?=$stack_trace_file_style?>"><?=$line['file']?> </span><span style="<?=$stack_trace_line_number_style?>">[<?=$line['line']?>] </span>
					<span style="<?=$stack_trace_line_style?>">
						<?=$line['code']?>
					</span>
				</li>
				<? endforeach;?>
			</ul>
		</div>
	
	<? endif;?>

	<div style="margin: 20px 0 0;<?=$box_style?>">
		<ul style="list-style-type: none; width: 100%; list-style-position: inside;margin:0;padding:0;border: solid 1px #555;<?=$list_style?>">
			<li style="margin-left: 0; padding: 10px 10px; background: <?=$trace_odd_item_background?>;background: #F00;">
				<span style="color: #DDD; <?=$trace_item_file_style?>">
					<?=$file?>
				</span>

				<span style="color: #FFF; padding-left: 5px; font-weight: bold;">
					[<?=$error_line?>]
				</span>
			</li>

			<? $index = 0; ?>
			<? foreach((array) $file_lines as $line_number => $lines) { ?>
				<? if($line_number == 0) continue; ?>
				<? $lines = HTMLHelper::escape($lines); ?>
				<li style="margin-left: 0;<? if($index == 0) { ?> padding-top: 10px !important; <? } ?> background: <?=($index % 2 == 0 ? $trace_even_item_background : $trace_odd_item_background)?> !important;<?=$trace_item_style?><? if($line_number == $error_line) echo $current_line_style;?><?=($index == count($file_lines)-1 ? $last_list_item_style : $list_item_style)?>">
					<span style="<?=$line_number_style?><? if($line_number == $error_line) echo $selected_line_number_style; ?>">
						# <?=$line_number?>
					</span>

					<span style="<?=$line_style?>">
						<!--<pre style="display: inline; border: 0; background: transparent;"><?=$lines?></pre>-->
						<?=str_replace("\t", '&nbsp;&nbsp;&nbsp;', $lines)?>
					</span>
				</li>
				<? $index++; ?>
			<? } ?>	
				
		</ul>
	</div>
	
	<? foreach($debug_vars as $varname) { ?>
	
		<? $var = $GLOBALS[$varname]; ?>
	
		<div style="<?=$box_style?>">
		
			<div style="margin: 0px; border: solid 1px #333; background: #777; font-weight: bold; padding: 10px; color: #FFF;">
				<?=$varname?>
			</div>
	
			<? VariableHelper::var_export_html($var); ?>
			
		</div>
	
	<? } ?>

</div>
