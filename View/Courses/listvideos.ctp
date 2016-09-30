<?php
$html = '<ul class="group-items">' . "\n";
foreach($videos['items'] as $video)	{
	$html .= '<li class="uploadItem group-item item"><a href="javascript:void(0)" id="'.$video['id'].'">' . $video['title'] . '</a></li>' . "\n";
}
$html .= '</ul>' . "\n";

$h = fopen(dirname(__FILE__) . DS . 'listvideos--cached.ctp', 'w');
fwrite($h, $html);
fclose($h);
echo $html;
