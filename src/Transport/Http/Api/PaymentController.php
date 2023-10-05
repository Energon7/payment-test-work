<?php

namespace App\Transport\Http\Api;

use App\Infrastructure\ArgumentResolver\DtoResolver\FromRequest;
use App\Infrastructure\Bus\CommandBus;
use App\Modules\Payment\Command\PayCommand\PayCommand;
use App\Modules\Product\Command\CalculatePrice\CalculatePriceCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends BaseApiController
{
    public function __construct(private readonly CommandBus $commandBus)
    {
    }

    #[Route(
        '/calculate-price',
        name: "calculate_price",
        methods: ["POST"]
    )]
    public function calculatePrice(#[FromRequest] CalculatePriceCommand $command): JsonResponse
    {
        return $this->apiJsonResponse(data: $this->commandBus->command($command));
    }

    #[Route(
        '/purchase',
        name: "calculate_price",
        methods: ["POST"]
    )]
    public function purchase(#[FromRequest] PayCommand $command): JsonResponse
    {
        $this->commandBus->command($command);
        return $this->apiJsonResponse();
    }
}