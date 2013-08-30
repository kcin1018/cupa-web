<?php

class Form_LeagueTeamEdit extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_team;
    protected $_leagueId;
    protected $_justLogo;

    public function __construct($leagueId, $team = null, $justLogo = false)
    {
        $this->_leagueId = $leagueId;
        $this->_team = $team;
        $this->_justLogo = $justLogo;

        parent::__construct();
    }

    public function init()
    {
        $this->addElementPrefixPath('Validate', APPLICATION_PATH . '/models/Validate/', 'validate');

        if(!$this->_justLogo) {
            $this->addElement('text', 'name', array(
                'filters' => array('StringTrim'),
                'required' => true,
                'value' => (empty($this->_team)) ? null : $this->_team->name,
                'label' => 'Name:',
                'class' => 'span5',
            ));

            $leagueInformationTable = new Model_DbTable_LeagueInformation();
            $info = $leagueInformationTable->fetchInformation($this->_leagueId);

            $userTable = new Model_DbTable_User();
            $users = array();
            foreach($userTable->fetchAllUsers() as $user) {
                $users[$user->id] = $user->first_name . ' ' . $user->last_name;
            }

            $leagueMemberTable = new Model_DbTable_LeagueMember();
            $leagueMemberYouthTable = new Model_DbTable_LeagueMemberYouth();
            $captains = array();
            if($this->_team) {
                if($info->is_youth) {
                    foreach($leagueMemberYouthTable->fetchAllByType($this->_leagueId, 'coach', $this->_team->id) as $member) {
                        $user = $userTable->fetchUserBy('email', $member['email']);
                        $captains[] = $user->id;
                    }
                } else {
                    foreach($leagueMemberTable->fetchAllByType($this->_leagueId, 'captain', $this->_team->id) as $member) {
                        $user = $userTable->find($member['user_id'])->current();
                        $captains[] = $user->id;
                    }
                }
            }

            $this->addElement('multiselect', 'captains', array(
                'validators' => array(
                    array('InArray', false, array(array_keys($users))),
                ),
                'required' => true,
                'label' => ($info->is_youth == 0) ? 'Captains:' : 'Coaches:',
                'class' => 'span6 select2',
                'multiOptions' => $users,
                'value' => (empty($captains)) ? null : $captains,
                'data-placeholder' => ($info->is_youth == 0) ? 'Select one or more captains' : 'Select one or more coaches',
            ));

            $this->addElement('text', 'color', array(
                'filters' => array('StringTrim'),
                'required' => true,
                'label' => 'Color:',
                'class' => 'span3',
                'value' => (empty($this->_team)) ? null : $this->_team->color,
                'description' => 'Enter the color of the team.',
            ));

            $this->addElement('text', 'color_code', array(
                'filters' => array('StringTrim'),
                'required' => true,
                'value' => (empty($this->_team)) ? null : $this->_team->color_code,
                'class' => 'span2 colorpicker',
                'data-color-format' => 'hex',
                'style' => 'text-align: center;',
                'label' => 'Select the color:',
            ));
        }

        $this->addElement('file', 'logo', array(
            'label' => 'Team Logo:',
            'required' => false,
            'validators' => array(
                array('Count', false, 1),
                array('Extension', false, 'jpg,png,gif'),
            ),
            'valueDisabled' => true,
        ));

/*
        if(!empty($this->_team)) {
            $this->addElement('text', 'final_rank', array(
                'filters' => array('digits'),
                'required' => false,
                'label' => 'Final Rank:',
                'class' => 'span1',
                'style' => 'text-align: center;',
                'value' => (empty($this->_team)) ? null : $this->_team->final_rank,
                'description' => 'Enter the ranking when the league is done.',
            ));
        }
*/

        $this->addElement('button', 'save', array(
            'type' => 'submit',
            'label' => (empty($this->_team)) ? 'Create' : 'Save',
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'escape' => false,
            'icon' => 'hdd',
            'whiteIcon' => true,
            'iconPosition' => Twitter_Bootstrap_Form_Element_Button::ICON_POSITION_LEFT,
        ));

        $this->addElement('submit', 'cancel', array(
            'type' => 'submit',
            'label' => 'Cancel',
            'escape' => false,
        ));

        if($this->_justLogo) {
            $questions = array('logo');
        } else {
            $questions = array('name', 'captains', 'color', 'color_code', 'logo');
        }
        $title = (empty($this->_team)) ? 'Add a Team' : 'Edit Team';
        $this->addDisplayGroup(
            $questions,
            'pickup_edit_form',
            array(
                'legend' => $title,
            )
        );

        $this->addDisplayGroup(
            array('save', 'cancel'),
            'pickup_edit_actions',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Actions'),
            )
        );
    }
}

