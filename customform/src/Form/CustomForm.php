<?php

namespace Drupal\customform\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CustomForm extends FormBase{

    public function getFormId()
    {
        return 'customform';
        
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        
        $form['element']=[
            '#type'=>'markup',
            '#markup'=>"<div class = success></div>"
        ];
        $form['title']= [
            '#type' => 'description',
            '#title' => 'Custom Form'
        ];

        $form['name']=[
            '#type'=> 'textfield',
            '#title'=> $this->t('Name')
        ];
        $form['gender']=[
            '#title'=> $this->t('Gender'),
            '#type' => 'radios',
            '#options'=>[
                0 => 'Male',
                1 => 'Female',
            ],
        ];
        $form['submit']=[
            '#type'=> 'submit',
            '#value'=> $this->t('Submit'),
            '#ajax'=>['callback'=>'::submitdata'],
        ];
        $form['#attached']['library'][] = 'customform/customform_js';
        
        return $form;
        
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        
    }
    public function submitdata(array &$form, FormStateInterface $form_state){
        $ajax_response = new AjaxResponse();
        $database = \Drupal::database();
        $fields = $form_state->getValues();
        $data['name'] = $fields['name'];
        $data['gender'] = $fields['gender'];
        $database->insert('customformtable')->fields($data)->execute();
        $ajax_response->addCommand(new HtmlCommand('.success','Submitted successfully'));
        return $ajax_response;
    }
}