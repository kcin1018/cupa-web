<?php

class Cupa_Model_DbTable_UserProfile extends Zend_Db_Table
{
    protected $_name = 'user_profile';
    protected $_primary = 'user_id';

    public function isEighteenOrOver($userId, $date)
    {
        $userProfile = $this->find($userId)->current();
        
        if(isset($userProfile->birthday)) {
            list($year, $month, $day) = explode("-", $userProfile->birthday);
            $age = (date("md", strtotime($date)) < $month.$day) ? date("Y", strtotime($date)) - $year - 1 : date("Y", strtotime($date)) - $year;
            return ($age >= 18) ? true : false;
        }
        
        return false;
    }
    
}
