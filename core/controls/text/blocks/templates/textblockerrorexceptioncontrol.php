<?=TextBlockErrorExceptionControl::_format_block('ERROR')?> 

Site: <?=ZPHP::get_config('site_name')?>

Request URL: <?=ZPHP::get_actual_uri()?> 

Message: <?=$message?>

<?php if(!empty($stack_trace)): ?>
<?=TextBlockErrorExceptionControl::_format_block('Stack Trace')?> 
<?php foreach($stack_trace as $index => $line): ?> 
# <?=($index+1)?> <?=$line['file']?> [<?=$line['line']?>] 

  <?=$line['code']?>

<?php endforeach;?>
<?php endif;?> 

<?=TextBlockErrorExceptionControl::_format_block($file_error_title)?> 
<?php $index = 0; ?><?php foreach((array) $file_lines as $line_number => $lines): ?><?php if($line_number == 0) continue; ?> 
# <?=str_pad($line_number, 4, ' ', STR_PAD_RIGHT)?><?=($line_number == $error_line ? '*' : ' ')?><?=$lines?>
<?php $index++; ?><?php endforeach; ?>	

<?php foreach($debug_vars as $varname): ?> 
<?=TextBlockErrorExceptionControl::_format_block('Variable $'.$varname)?>  

<?=var_export($GLOBALS[$varname], true)?> 

<?php endforeach; ?>

