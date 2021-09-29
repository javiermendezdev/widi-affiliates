<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationList;

final class ConstraintViolationListException extends \Exception
{

    public function __construct(ConstraintViolationList $errorList) {

        $message = "";
        foreach($errorList as $error){
          $message .= (empty($message)?'':', ') . $error->getMessage();
        }
        parent::__construct($message);
    }
}
