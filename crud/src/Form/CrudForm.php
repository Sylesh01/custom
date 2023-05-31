<?php

namespace Drupal\crud\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;



class CrudForm extends FormBase {

    public function getFormId()
    {
        return 'crud_form';
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $conn = Database::getConnection();
        $record = array();
        if (isset($_GET['num'])) {
            $record = $conn->select('crud_table', 'm')
            ->condition('id', $_GET['num'])
            ->fields('m')->execute()->fetchAll();            
        }

        $form['CRUD Form'] = [
            '#type' => 'description',
            '#open' => 'false'
        ];
        $form['name'] = [
            '#type' => 'textfield',
            '#title'=> $this->t('Name'),
            '#default_value' => (isset($record[0]->name) && $_GET['num']) ? $record[0]->name:'',
        ];
        
        $form['phone_number'] = [
            '#type' => 'textfield',
            '#title'=> $this->t('Phone Number'),
            '#default_value' => (isset($record[0]->number) && $_GET['num']) ? $record[0]->number:'',
            '#attributes' => ['id' => 'phone-number-field'],
        ];
        $form['element']=[
            '#type'=>'markup',
            '#markup'=>"<div class = phoneerr></div>"
        ];
        $form['email'] = [
            '#type' => 'email',
            '#title'=> $this->t('Email'),
            '#default_value' => (isset($record[0]->email, $_GET['num'])) ? $record[0]->email:'',
            '#attributes' => ['id' => 'edit-email'],
            '#ajax' => [
                'callback' => '::validateEmail',
                'event' => 'blur',
                'wrapper' => 'edit-email-error',      
                'progress' => [
                    'type' => 'throbber',
                    'message' => NULL,
                ],
            ],
            
        ];
        $form['ajax_respose'] = [
            '#prefix' => '<div id="edit-email-error">',
            '#suffix' => '</div>',
        ];  

        $form['gender'] = [
            '#type' => 'radios',
            '#title'=> $this->t('Gender'),
            '#default_value' => (isset($record[0]->gender)&&($_GET['num'])) ? $record[0]->gender: NULL,
            '#options' => [
                0 => 'Male',
                1 => 'Female'
            ]
        ];
        $form['submit'] = [
            '#type' => 'submit',
            '#value'=> $this->t('Save'),
            // '#ajax' => ['callback' => '::validation']
        ];

        $form['#attached']['library'][] = 'crud/crud_js';
        return $form;

    }
    
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $fields = $form_state->getValues();
        $data = [
            'name' => $fields['name'],
            'number' => $fields['phone_number'],
            'email' => $fields['email'],
            'gender' => $fields['gender']
        ];

        if (isset($_GET['num'])) {
            $query = \Drupal::database();
            $query->update('crud_table')
                ->fields($data)
                ->condition('id', $_GET['num'])
                ->execute();
                \Drupal::messenger()->addStatus("succesfully updated");
            $form_state->setRedirect('crud.display');
  
        }
        else {
            
        
        $conn = Database::getConnection();
        $conn->insert('crud_table')->fields($data)->execute();
        // \Drupal::messenger()->addStatus("succesfully saved");
        $response = new RedirectResponse("/crud-display");
        $response->send();
        }
    }
    public function validateEmail(array &$form, FormStateInterface $form_state){

        $response = new AjaxResponse();
        // $response->addCommand(new HtmlCommand('#edit-email-error', 'Please enter a valid email address.'));
        $email = $form_state->getValue('email');

        if(is_null($email)){
            $response->addCommand(new HtmlCommand('#edit-email-error', 'Please enter a valid email address.'));
        }
        elseif (!\Drupal::service('email.validator')->isValid($email)) {
            $response->addCommand(new HtmlCommand('#edit-email-error', 'Please enter a valid email address.'));
        }
        else {
            // Check if the email is already in use.
            $query = \Drupal::database()->select('crud_table', 'c');
            $query->fields('c', ['email']);
            $query->condition('c.email', $email,'=');
            $result = $query->execute()->fetchAll();
            if (!empty($result)) {
            $response->addCommand(new HtmlCommand('#edit-email-error', 'This email address is already in use.'));
            }
            else {
                // Clear the error message if the email is valid and not in use.
            $response->addCommand(new HtmlCommand('#edit-email-error', ''));
            }
        }
        $response->addCommand(new InvokeCommand(NULL, 'focus', ['#']));
    return $response;
    }
}