<?php

class Model_DbTable_LeagueLocation extends Zend_Db_Table
{
    protected $_name = 'league_location';
    protected $_primary = 'id';
    
    public function fetchLocations($leagueId)
    {
        $select = $this->select()
                       ->where('league_id = ?', $leagueId);
        
        $data = array();
        foreach($this->fetchAll($select) as $row) {
            $data[$row['type']] = $row->toArray();
        }
        
        return $data;

    }
    
    public function fetchByType($leagueId, $type)
    {
        $select = $this->select()
                       ->where('league_id = ?', $leagueId)
                       ->where('type = ?', $type);
        
        return $this->fetchRow($select);
    }
}
