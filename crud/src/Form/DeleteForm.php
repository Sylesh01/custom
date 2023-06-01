<?php

namespace Drupal\crud\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class DeleteForm extends ConfirmFormBase {

    /**
     * ID of the item to delete.
     *
     * @var int
     */
    protected $id;
  
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, string $id = NULL) {
      $this->id = $id;
      return parent::buildForm($form, $form_state);
    }
  
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $query = \Drupal::database();
        $query->delete('crud_table')
              ->condition('id',$this->id)
            ->execute();
            \Drupal::messenger()->addStatus("succesfully deleted");
        $form_state->setRedirect('crud.display');
    }
  
    /**
     * {@inheritdoc}
     */
    public function getFormId() : string {
      return "delete_form";
    }
  
    /**
     * {@inheritdoc}
     */
    public function getCancelUrl() {
      return new Url('crud.display');
    }
  
    /**
     * {@inheritdoc}
     */
    public function getQuestion() {
      return $this->t('Do you want to delete %id?', ['%id' => $this->id]);
    }
  
  }