<?php

class My_View_Helper_LeagueBar extends Zend_View_Helper_Abstract
{

    private $_totalWidth = 255;

    public function leagueBar($name, $current, $total)
    {
        $number = (int)(($current / $total) * 100);
        $width = (int)($this->_totalWidth * ($number / 100));

        $ratio = ($current / $total) * 100;
        if($ratio < 33) {
            $color = 'success';
        } else if($ratio < 66) {
            $color = 'warning';
        } else {
            $color = 'danger';
        }

        $string = '<div>' . "\n";
        $string .= '    <strong>' .  ($total - $current) . '</strong> ' . $name . ' left' . "\n";
        $string .= '    <div class="progress progress-' . $color . '">' . "\n";
        $string .= '        <div class="bar" style="width: ' . number_format($ratio) . '%"></div>' . "\n";
        $string .= '    </div>';
        $string .= '</div>';

        return $string;
    }
}
