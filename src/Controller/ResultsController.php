<?php

declare(strict_types=1);

namespace App\Controller;

use App\WebApi\Result;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ResultsController
 * @package App\Controller
 */
#[Route(path: "/results", name: "results_")]
class ResultsController extends AbstractController
{
    /**
     * @param Result $resultService
     */
    public function __construct(
        private Result $resultService,
        private LoggerInterface $logger
    ) {}

    /**
     * @return Response
     */
    #[Route(path: "", name: "all", methods: ["GET"])]
    public function all(): Response
    {
        try {
            $result = $this->resultService->getAll();
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());

            return $this->json(
                ["error" => 'There was an error while processing the request.'],
                $exception->getCode() ?: JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json($result, JsonResponse::HTTP_OK, ["Content-Type" => "application/json"]);
    }
}
