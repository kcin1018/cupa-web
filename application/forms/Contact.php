<?php

class Form_Contact extends Twitter_Bootstrap_Form_Horizontal
{
    public function init()
    {
        $this->addElementPrefixPath('Validate', APPLICATION_PATH . '/models/Validate/', 'validate');

        $this->addElement('text', 'from', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('EmailAddress', true),
            ),
            'class' => 'input-xlarge',
            'required' => true,
            'label' => 'From:',
        ));

        $toSelection = $this->getContacts();

        $this->addElement('select', 'to', array(
            'validators' => array(
                array('InArray', false, array(array_keys($toSelection))),
            ),
            'class' => 'input-xlarge',
            'required' => true,
            'label' => 'To:',
            'multiOptions' => $toSelection,
        ));

        $this->addElement('text', 'subject', array(
            'filters' => array('StringTrim'),
            'class' => 'input-xlarge',
            'required' => true,
            'label' => 'Subject:',
            'value' => '[CUPA Information] More Information',
        ));

        $this->addElement('textarea', 'content', array(
            'filters' => array('StringTrim'),
            'class' => 'span6',
            'style' => 'height: 100px;',
            'required' => true,
            'label' => 'Message Content:',
        ));

        $this->addElement('button', 'send', array(
            'type' => 'submit',
            'label' => 'Send Email',
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'escape' => false,
            'icon' => 'envelope',
            'whiteIcon' => true,
            'iconPosition' => Twitter_Bootstrap_Form_Element_Button::ICON_POSITION_LEFT,
        ));

        $this->addDisplayGroup(
            array('from', 'to', 'subject', 'content'),
            'contact_form',
            array(
                'legend' => 'Contact CUPA',
            )
        );

        $this->addDisplayGroup(
            array('send'),
            'contact_actions',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Actions')
            )
        );
    }

    /**
     * Handle getting the contacts from the database
     *
     * @return array
     */
    private function getContacts()
    {
        // link to the contact table
        $contactTable = new Model_DbTable_Contact();

        $data = array();
        foreach($contactTable->fetchAll() as $contact) {
            // for each contact add it to the data array
            $data[$contact->email] = $contact->name;
        }

        // return the final array
        return $data;
    }
}
