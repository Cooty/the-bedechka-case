<?php


namespace App\Service\Admin;

use Symfony\Component\Form\FormInterface;

abstract class AbstractFormHandler
{
    protected $entity;

    /**
     * @var FormInterface
     */
    protected $form;

    public function __construct($entity, FormInterface $form)
    {
        $this->entity = $entity;
        $this->form = $form;
    }

    abstract public function getForm();
}