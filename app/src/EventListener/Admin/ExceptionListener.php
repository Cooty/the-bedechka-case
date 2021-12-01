<?php

namespace App\EventListener\Admin;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class ExceptionListener
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $isDebug;

    /**
     * @param Security $security
     * @param Environment $twig
     * @param LoggerInterface $logger
     * @param bool $isDebug
     */
    public function __construct(
        Security $security,
        Environment $twig,
        LoggerInterface $logger,
        bool $isDebug
    )
    {
        $this->security = $security;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }

    private function isAdmin(?UserInterface $user): bool
    {
        if(empty($user)) {
            return false;
        }

        return in_array(User::ROLE_ADMIN, $user->getRoles());
    }

    public function onKernelException(ExceptionEvent $event)
    {
        if(!$this->isDebug) {
            $user = $this->security->getUser();

            if($this->isAdmin($user)) {
                $exception = $event->getException();
                $response = new Response();

                if ($exception instanceof HttpExceptionInterface) {
                    try {
                        $html = $this->twig->render('admin/error/404.html.twig');
                    } catch (\Exception $e) {
                        $html = 'Error 500';
                        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
                        $this->logger->error($e->getMessage().' '.$e->getTraceAsString());
                    }
                    $response->setContent($html);
                    $response->setStatusCode($exception->getStatusCode());
                    $response->headers->replace($exception->getHeaders());
                } else {
                    try {
                        $html = $this->twig->render('admin/error/500.html.twig');
                    } catch (\Exception $e) {
                        $html = 'Error 500';
                        $this->logger->error($e->getMessage().' '.$e->getTraceAsString());
                    }

                    $response->setContent($html);
                    $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $event->setResponse($response);
            }
        }
    }
}