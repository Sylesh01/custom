<?php

namespace Drupal\crud\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;

class Displaytable extends ControllerBase{

    public function displaytable(){
        $array = [];
        $query = \Drupal::database()->select('crud_table','c')->fields('c',['id','name','number','email','gender'])
        ->execute()->fetchAll();
        foreach($query as $data){
            $array[] = ['id'=> $data->id,
                    'name' => $data->name,
                    'number' => $data->number,
                    'email' => $data -> email,
                    'gender' => $data -> gender,
                    'delete' => Link::fromTextAndUrl('Delete',Url::fromUserInput('/crud-form/delete/'.$data->id)),
                    'edit' => Link::fromTextAndUrl('Edit',Url::fromUserInput('/crud-form/edit?num='.$data->id))];
        }
        $header = [
            'id' => $this->t('Id'),
            'name' => $this->t('Name'),
            'number'=>$this->t('Number'),
            'email' => $this->t('Email'),
            'gender' => $this->t('Gender'),
            'opn1' => $this->t('Operations'),
            'opn2' => $this->t('Operations')
          ];
          
          
        $form['Data'] = [
            '#type' => 'table',
            '#header'=> $header,
            '#rows'=> $array,
            '#empty' => $this->t('No users found'),
        ];
        return $form;
    }
}