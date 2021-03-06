<?php

class Model_DbTable_TournamentDivision extends Zend_Db_Table
{
    protected $_name = 'tournament_division';
    protected $_primary = 'id';

    public function fetchByName($divisionName)
    {
    	if($divisionName == 'women') {
    		$divisionName = 'womens';
    	}

    	$select = $this->select()
    	               ->where('name = ?', $divisionName);

    	return $this->fetchRow($select);
    }

    public function fetchDivisions()
    {
        $select = $this->select()
                       ->order('weight ASC');

        return $this->fetchAll($select);
    }
}
