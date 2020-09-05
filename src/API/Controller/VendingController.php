<?php

declare(strict_types=1);

namespace API\Controller;

use API\Service\FormatRequestService;
use API\Service\FormatResponseService;
use API\Service\VendingService;
use Swagger\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class VendingController
{
    private $vendingService;

    /**
     * @var FormatRequestService
     */
    private $formatRequestService;

    /**
     * @var FormatResponseService
     */
    private $formatResponseService;

    public function __construct(
        VendingService $vendingService,
        FormatRequestService $formatRequestService,
        FormatResponseService $formatResponseService
    ) {
        $this->vendingService = $vendingService;
        $this->formatRequestService = $formatRequestService;
        $this->formatResponseService = $formatResponseService;
    }

    /**
     * @OA\Post(tags={"customer"}, summary="Insert new coin",
     *      @OA\Parameter(
     *          name="body",
     *          in="body",
     *          description="JSON Payload",
     *          required=true,
     *          type="json",
     *          format="application/json",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(property="value", type="float", example="0.25")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Coin inserted"
     *      )
     * )
     */
    public function postCoinInsertAction(Request $request)
    {
        $payload = $this->formatRequestService->request($request);
        $this->vendingService->insertCoin((float) $payload['value']);

        return $this->formatResponseService->response([]);
    }

    /**
     *   @OA\Get(tags={"customer"}, summary="Return all inserted coins",
     *      @OA\Response(
     *          response=200,
     *          description="Get inserted coins",
     *          @OA\Schema(
     *               type="object",
     *               example={0.25, 0.10, 0.05}
     *          )
     *      )
     *   )
     */
    public function getCoinReturnAction(Request $request)
    {
        $coins = $this->vendingService->returnAllCoins();

        return $this->formatResponseService->response($coins);
    }

    /**
     *   @OA\Get(tags={"customer"}, summary="Check how much money I have inserted",
     *      @OA\Response(
     *          response=200,
     *          description="Get pocket status",
     *          @OA\Schema(
     *               type="object",
     *               example={"money": 1.25, "coins": {1, 0.25}}
     *          )
     *      )
     *   )
     */
    public function getCoinStatusAction(Request $request)
    {
        $status = $this->vendingService->coinStatus();

        return $this->formatResponseService->response($status);
    }


    /**
     *   @OA\Get(tags={"customer"}, summary="Buy an existing item",
     *      @OA\Parameter(
     *          name="name",
     *          in="path",
     *          required=true,
     *          enum={"SODA","JUICE","WATER"},
     *          default="SODA",
     *          type="string",
     *          description="The Item name"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Get coins",
     *          @OA\Schema(
     *               type="object",
     *               example={"SODA", 0.10, 0.05}
     *          )
     *      )
     *   )
     */
    public function getItemBuyAction(Request $request)
    {
        $change = $this->vendingService->buyItem($request->get('name'));

        return $this->formatResponseService->response($change);
    }

    /**
     *   @OA\Get(tags={"customer"}, summary="Check items info, price and amount of each one",
     *      @OA\Response(
     *          response=200,
     *          description="Get items information",
     *          @OA\Schema(
     *               type="object",
     *               example={{"item": "WATER", "price": 0.65, "amount": 10}, {"item": "SODA", "price": 1.5, "amount": 0}, {"item": "JUICE", "price": 1, "amount": 4}}
     *          )
     *      )
     *   )
     */
    public function getItemStatusAction(Request $request)
    {
        $status = $this->vendingService->itemStatus();

        return $this->formatResponseService->response($status);
    }

    /**
     *   @OA\Get(tags={"service"}, summary="Check status of everything",
     *      @OA\Response(
     *          response=200,
     *          description="Get status",
     *          @OA\Schema(
     *               type="object",
     *               example={"pocket": {"money": 0,"coins": {}},"machine": {"total": 24.85,"coins": {{"value": 1,"amount": 17},{"value": 0.25,"amount": 26},{"value": 0.1,"amount": 8},{"value": 0.05,"amount": 11}}},"items": {{"item": "WATER","price": 0.65,"amount": 10},{"item": "SODA","price": 1.5,"amount": 0},{"item": "JUICE","price": 1,"amount": 4}}}
     *          )
     *      )
     *   )
     */
    public function getServiceStatusAction(Request $request)
    {
        $status = $this->vendingService->serviceStatus();

        return $this->formatResponseService->response($status);
    }

}
