<?php

namespace App\Subscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Exception\ConstraintViolationListException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionSubscriber
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        //Eliminate this type of exceptions:
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return;
        }

        if (($message = $exception->getMessage())) {
            $this->logger->error($message);
            if (getenv('APP_DEBUG')) {
                $this->logger->error($exception->getTraceAsString());
            }
        }

        $title = $exception->getMessage();
        $detail = $exception->getTraceAsString();
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        //$code = $exception->getCode();

        //TODO: Add new custom api exception
        if ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
        }

        if($exception instanceof ConstraintViolationListException){
            $status = Response::HTTP_BAD_REQUEST;
        }

        $response = new JsonResponse(
            [
                //"type" => "https://example-errors/code-errors/$code.html",
                "title" => $title,
                "detail" => $detail,
                "status" => $status,
            ],
            $status
        );
        $response->headers->set('Content-Type', 'application/problem+json');
        $event->setResponse($response);
    }
}
