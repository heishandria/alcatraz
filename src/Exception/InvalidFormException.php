<?php


namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Form\Form;

/**
 * Class InvalidFormException
 * 
 * @package App\Exception
 */
class InvalidFormException extends HttpException
{
    /**
     * @var null
     */
    protected $form;

    /**
     * InvalidFormException constructor.
     *
     * @param int $statusCode
     * @param string $message
     * @param null $form
     */
    public function __construct(int $statusCode, string $message, $form = null)
    {
        parent::__construct($statusCode, $message);
        $this->form = $form;
    }

    /**
     * @return array|null
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param Form $form
     * @return array
     */
    public function getErrorMessages(Form $form)
    {
        $errors = array();
       
        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            }
            else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}