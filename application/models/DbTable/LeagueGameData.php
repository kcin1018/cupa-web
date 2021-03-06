<?php

class Model_DbTable_LeagueGameData extends Zend_Db_Table
{
    protected $_name = 'league_game_data';
    protected $_primary = 'id';

    public function fetchOutcome($gameId, $teamId)
    {
        $select = $this->getAdapter()->select()
            ->from(array('lg' => $this->_name), array())
            ->join(array('lgdteam' => 'league_game_data'), 'lgdteam.league_game_id = lg.id', array('score AS team'))
            ->join(array('lgdopponent' => 'league_game_data'), 'lgdopponent.league_game_id = lg.id', array('score AS opponent'))
            ->where('lg.id = ?', $gameId)
            ->where('lgdteam.league_team_id = ?', $teamId)
            ->where('lgdopponent.league_team_id <> ?', $teamId)
            ->where('lgdteam.score > 0 OR lgdopponent.score > 0');

        $result = $this->getAdapter()->fetchRow($select);

        if($result['team'] == $result['opponent']) {
            return 'tie';
        }

        return ($result['team'] > $result['opponent']) ? 'win' : 'loss';
    }

    public function fetchGameData($gameId, $type = null)
    {
        $select = $this->select()
                       ->where('league_game_id = ?', $gameId)
                       ->order('type');

        if(!empty($type)) {
            $select->where('type = ?', $type);
            return $this->fetchRow($select);
        }

        return $this->fetchAll($select);
    }

    public function isUnique($gameId, $homeTeamId, $awayTeamId)
    {
        if(is_object(($gameId)) and isset($gameId->id)) {
            $gameId = $gameId->id;
        }

        // if the game id doesn't exist then its unique
        if(!is_numeric($gameId)) {
            return true;
        }

        // fetch the game data
        $gameData = $this->fetchGameData($gameId);

        // check to see if the game as teams
        if(empty($gameData[0])) {
            return true;
        }

        // check to see if the game data has the same away vs home teams
        if($homeTeamId == $gameData[0]->league_team_id and $awayTeamId == $gameData[1]->league_team_id) {
            return false;
        }

        // else is unique
        return true;
    }

    public function addGameData($gameId, $type, $teamId)
    {
        $result = $this->fetchGameData($gameId, $type);

        if(!$result) {
            $this->insert(array(
                'league_game_id' => $gameId,
                'type' => $type,
                'league_team_id' => $teamId,
                'score' => 0,
            ));
        } else {
            $result->league_team_id = $teamId;
            $result->score = 0;
            $result->save();
        }
    }
}
