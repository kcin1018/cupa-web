<?php

class Model_DbTable_Tournament extends Zend_Db_Table
{
    protected $_name = 'tournament';
    protected $_primary = 'id';

    public function isUnique($year, $name)
    {
        $select = $this->select()
                       ->where('year = ?', $year)
                       ->where('name = ?', $name);

        $result = $this->fetchRow($select);
        if(!$result) {
            return true;
        }

        return false;
    }

    public function createBlankTournament($year, $name, $directorIds)
    {
        $tournament = $this->createRow();
        $tournament->name = $name;
        $tournament->year = $year;
        $tournament->display_name = $name;
        $tournament->is_visible = 0;
        $tournament->save();

        $tournamentInfoTable = new Model_DbTable_TournamentInformation();
        $tournamentInfo = $tournamentInfoTable->createRow();
        $tournamentInfo->tournament_id = $tournament->id;
        $tournamentInfo->start = date('Y-m-d');
        $tournamentInfo->end = date('Y-m-d');
        $tournamentInfo->bid_due = date('Y-m-d');
        $tournamentInfo->cost = 100;
        $tournamentInfo->description = 'Enter the tournament description here.';
        $tournamentInfo->schedule_text = 'Enter any schedule information here.';
        $tournamentInfo->location = 'Enter the name of the field location';
        $tournamentInfo->location_street = '9999 street name';
        $tournamentInfo->location_city = 'Cincinnati';
        $tournamentInfo->location_state = 'OH';
        $tournamentInfo->location_zip = '45209';
        $tournamentInfo->save();

        $tournamentMemberTable = new Model_DbTable_TournamentMember();
        foreach($directorIds as $directorId) {
            $tournamentMember = $tournamentMemberTable->createRow();
            $tournamentMember->tournament_id = $tournament->id;
            $tournamentMember->user_id = $directorId;
            $tournamentMember->weight = $tournamentMemberTable->getHighestWeight($tournament->id);
            $tournamentMember->type = 'director';
            $tournamentMember->save();
        }

        $tournamentUpdateTable = new Model_DbTable_TournamentUpdate();
        $tournamentUpdate = $tournamentUpdateTable->createRow();
        $tournamentUpdate->tournament_id = $tournament->id;
        $tournamentUpdate->posted = date('Y-m-d H:i:s');
        $tournamentUpdate->title = 'Tournament Page Created';
        $tournamentUpdate->content = 'The tournament page has been created  you may edit this to enter initial info.';
        $tournamentUpdate->save();

        return $tournament;

    }

    public function fetchMostCurrentYear($name, $isAdmin = false)
    {
        $select = $this->select()
                       ->where('name = ?', $name)
                       ->order('year DESC');

        if(!$isAdmin) {
            $select->where('is_visible = ?', 1);
        }

        $result = $this->fetchRow($select);
        if($result) {
            return $result->year;
        }

        return null;
    }

    public function fetchTournament($year, $name, $showHidden = false)
    {
        if(empty($year)) {
            return null;
        }

        $select = $this->select()
                       ->where('year = ?', $year)
                       ->where('name = ?', $name);

        if(!$showHidden) {
            $select = $select->where('is_visible = ?', 1);
        }

        return $this->fetchRow($select);
    }

    public function fetchAllTournamentsForPage($showHidden = false)
    {
        $select = $this->getAdapter()->select()
                       ->from(array('t' => $this->_name), array('*'))
                       ->join(array('ti' => 'tournament_information'), 'ti.tournament_id = t.id', array('start', 'end', 'tenative_date'))
                       ->order('start ASC')
                       ->order('name');

        if(!$showHidden) {
            $select->where('t.is_visible = ?', 1);
        }

        $data = array();
        foreach($this->getAdapter()->fetchAll($select) as $row) {
            $data[$row['name']] = $row;
            $data[$row['name']]['type'] = 'tournament';
        }

        usort($data, function($a, $b) {
            if($a['year'] == $b['year']) {
                return ($a['start'] > $b['start']) ? 1 : -1;
            }
            return ($a['year'] > $b['year']) ? -1 : 1;
        });

        return $data;
    }

    public function fetchAllTournamentsWithDirectors()
    {
        $select = $this->getAdapter()->select()
               ->from(array('t' => $this->_name), array('*'))
               ->joinLeft(array('tm' => 'tournament_member'), 'tm.tournament_id = t.id', array('type', 'tm.name AS director', 'tm.email AS director_email'))
               ->joinLeft(array('u' => 'user'), 'u.id = tm.user_id', array("CONCAT(u.first_name, ' ', u.last_name) AS director_user", 'u.email AS director_user_email'))
               ->where('t.is_visible = ?', 1)
               ->where('tm.type = ?', 'director')
               ->where('t.year >= ?', date('Y') - 1)
               ->order('t.name')
               ->order('t.year DESC');

        $data = array();
        foreach($this->getAdapter()->fetchAll($select) as $row) {
            if(empty($data[$row['name']])) {
                $data[$row['name']] = array(
                    'name' => $row['display_name'],
                    'year' => $row['year'],
                    'directors' => array(
                        (empty($row['director_user'])) ? $row['director'] : $row['director_user'] => (empty($row['director_user_email'])) ? $row['director_email'] : $row['director_user_email'],
                    ),
                );
            } else if($row['year'] == $data[$row['name']]['year']) {
                $directorName = (empty($row['director_user'])) ? $row['director'] : $row['director_user'];
                $data[$row['name']]['directors'][$directorName] = (empty($row['director_user_email'])) ? $row['director_email'] : $row['director_user_email'];
            }
        }

        return $data;
    }

    public function fetchDistinctTournaments()
    {
        $select = $this->select()
                       ->group('name')
                       ->order('name');

        return $this->fetchAll($select);
    }
}
