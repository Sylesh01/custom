<?php

namespace Drupal\multistep_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;



/**
 * Configure example settings for this site.
 */
class MultistepForm extends FormBase {

   /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'multistep_form';
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state){
  if ($form_state->has('page') && $form_state->get('page') == 2) {
    return self::formPageTwo($form, $form_state);
  }
  $form_state->set('page', 1);
  $page = $form_state->get('page');
  $form['description'] = [
    '#type' => 'item',
    '#title' => $this->t("Page $page",),
  ];


  $form['first_name'] = [
    '#type' => 'textfield',
    '#title' => $this->t('First Name'),
    '#default_value' => $form_state->getValue('first_name', ''),
  ];

  $form['last_name'] = [
    '#type' => 'textfield',
    '#title' => $this->t('Last Name'),
    '#default_value' => $form_state->getValue('last_name', ''),
  ];


  $form['actions'] = [
    '#type' => 'actions',
  ];

  $form['actions']['next'] = [
    '#type' => 'submit',
    '#button_type' => 'primary',
    '#value' => $this->t('Next'),
    '#submit' => ['::submitPageOne'],
    '#validate' => ['::validatePageOne'],
  ];

  return $form;

}

/**
 * @param array $form
 *   An associative array containing the structure of the form.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The current state of the form.
 */
public function validatePageOne(array &$form, FormStateInterface $form_state) {
  $title = $form_state->getValue('first_name');
  if (strlen($title) < 5) {
    $form_state->setErrorByName('first_name', $this->t('The first name must be at least 5 characters long.'));
  }
}
/**
 * @param array $form
 *   An associative array containing the structure of the form.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The current state of the form.
 */
public function submitPageOne(array &$form, FormStateInterface $form_state) {
  $form_state
    ->set('page_values', [
      'first_name' => $form_state->getValue('first_name'),
      'last_name' => $form_state->getValue('last_name'),
    ])
    ->set('page', 2)
    ->setRebuild(TRUE);
}

  /** 
   * {@inheritdoc}
   */
  public function formPageTwo(array &$form, FormStateInterface $form_state) {

    $form['description'] = [
      '#type' => 'item',
      '#title' => $this->t('Page @page',['@page'=>$form_state->get('page')]),
    ];
    
    $form['color'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Favorite color'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('color', ''),
    ];
    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
      '#submit' => ['::pageTwoBack'],
      '#limit_validation_errors' => [],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }
  
  /**
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function pageTwoBack(array &$form, FormStateInterface $form_state) {
    $form_state
      ->setValues($form_state->get('page_values'))
      ->set('page', 1)
      ->setRebuild(TRUE);
  }
  
  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    // validate form
  }
  
  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $this->messenger()->addStatus($this->t('The form has been submitted'));
  }
}
