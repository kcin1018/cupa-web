<?php

class Form_News extends Zend_Form
{

    public function init()
    {
        $this->addElementPrefixPath('Validate', APPLICATION_PATH . '/models/Validate/', 'validate');

        $newsCategoryTable = new Model_DbTable_NewsCategory();
        $categories = array();
        foreach($newsCategoryTable->fetchAllCategories() as $category) {
            $categories[$category->id] = $category->name;
        }
        
        $this->addElement('checkbox', 'is_visible', array(
            'label' => 'Is Visible:',
        ));
        
        $this->addElement('select', 'category', array(
            'validators' => array(
                array('InArray', false, array(array_keys($categories))),
            ),
            'required' => true,
            'label' => 'Category:',
            'multiOptions' => $categories,
        ));
        
        $this->addElement('text', 'title', array(
            'filters' => array('StringTrim'),
            'required' => true,
            'label' => 'Title:',
        ));
        
        $this->addElement('text', 'url', array(
            'filters' => array('StringTrim'),
            'label' => 'Url:',
            'description' => '(optional)',
        ));
       
        $this->addElement('textarea', 'info', array(
            'filters' => array('StringTrim'),
            'required' => true,
            'validators' => array(
                array('StringLength', false, array(1, 255)),
            ),
            'label' => 'Short Description:',
        ));
        
        $this->addElement('text', 'remove_at', array(
            'filters' => array('StringTrim'),
            'required' => false,
            'label' => 'Remove At:',
        ));

        $this->addElement('textarea', 'content', array(
            'filters' => array('StringTrim'),
            'label' => 'Content:',
            'description' => '(optional)',
        ));
    }

    public function loadFromNews($news)
    {
        $this->getElement('is_visible')->setValue($news->is_visible);
        $this->getElement('category')->setValue($news->category_id);
        $this->getElement('title')->setValue($news->title);
        $this->getElement('url')->setValue($news->url);
        $this->getElement('info')->setValue($news->info);
        $this->getElement('remove_at')->setValue($news->remove_at);
        $this->getElement('content')->setValue($news->content);
    }

}

