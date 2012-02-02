<?php

class Cupa_Model_DbTable_LeagueAnswer extends Zend_Db_Table
{
    protected $_name = 'league_answer';
    protected $_primary = 'id';
    
    public function fetchShirts($leagueId)
    {
        $leagueQuestionTable = new Cupa_Model_DbTable_LeagueQuestion();
        $question = $leagueQuestionTable->fetchQuestion('shirt');
        
        $select = $this->getAdapter()->select()
                       ->from(array('la' => $this->_name), array('answer'))
                       ->joinLeft(array('lm' => 'league_member'), 'lm.id = la.league_member_id', array())
                       ->joinLeft(array('lt' => 'league_team'), 'lt.id = lm.league_team_id', array('color', 'text_code', 'color_code'))
                       ->where('lm.league_id = ?', $leagueId)
                       ->where('la.league_question_id = ?', $question->id);
        
        $data = array();
        foreach($this->getAdapter()->fetchAll($select) as $row) {
            if(!empty($row['color'])) {
                if(isset($data[$row['color']][$row['answer']])) {
                    $data[$row['color']][$row['answer']]++;
                } else { 
                    $data[$row['color']][$row['answer']] = 1;
                }

                if(!isset($data[$row['color']]['text_code'])) {
                    $data[$row['color']]['text_code'] = $row['text_code'];
                }
                if(!isset($data[$row['color']]['color_code'])) {
                    $data[$row['color']]['color_code'] = $row['color_code'];
                }
            }
        }
        
        return $data;
    }
    
    public function fetchAllAnswers($leagueMemberId, $leagueId)
    {
        $select = $this->select()
                       ->where('league_member_id = ?', $leagueMemberId);
        
        $data = array();
        $leagueQuestionTable = new Cupa_Model_DbTable_LeagueQuestion();
        foreach($this->fetchAll($select) as $row) {
            $question = $leagueQuestionTable->find($row['league_question_id'])->current();
            if($question) {
                $data[$question->name] = $row['answer'];
            }
            
        }
        
        return $data;
    }
    
}