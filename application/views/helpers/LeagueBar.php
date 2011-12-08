<?php

class My_View_Helper_LeagueBar extends Zend_View_Helper_Abstract
{
    
    private $_totalWidth = 255;
    
    public function leagueBar($name, $current, $total)
    {        
        $number = (int)(($current / $total) * 100);
        $width = (int)($this->_totalWidth * ($number / 100));
        
        $rHex = dechex(sprintf('%02d', $width));
        $gHex = dechex(sprintf('%02d', ($this->_totalWidth - $width)));
        $color = $rHex . $gHex . '00';
        
        $string = '<div class="bar-graph"><h3 class="graph-header">' . $name . '</h3><span class="spots">( ' . ($total - $current) . ' spots left )</span><br/>';
        $string .= '<div class="graph"><div class="bar" style="width: ' . $width . 'px; background-color: #' . $color . ';">&nbsp;</div></div></div>';
        
        return $string;
    }
}