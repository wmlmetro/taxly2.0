<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResourceController extends Controller
{
  protected FirsApiService $firsApiService;

  public function __construct(FirsApiService $firsApiService)
  {
    $this->firsApiService = $firsApiService;
  }

  /**
   * @OA\Get(
   *     path="/api/v1/resources/invoice-types",
   *     tags={"Resources"},
   *     security={{"sanctum":{}}},
   *     summary="Get available invoice types",
   *     description="Fetch a list of invoice types available from FIRS.",
   *     @OA\Response(
   *         response=200,
   *         description="List of invoice types successfully retrieved",
   *         @OA\JsonContent(type="object")
   *     ),
   *     @OA\Response(
   *         response=500,
   *         description="Failed to fetch invoice types"
   *     )
   * )
   */
  public function getInvoiceTypes()
  {
    try {
      $response = $this->firsApiService->getInvoiceTypes();
      return response()->json($response, 200);
    } catch (\Throwable $e) {
      Log::error('FIRS GetInvoiceTypes Error: ' . $e->getMessage());
      return response()->json(['message' => 'Failed to fetch invoice types.'], 500);
    }
  }

  /**
   * @OA\Get(
   *     path="/api/v1/resources/payment-means",
   *     tags={"Resources"},
   *     security={{"sanctum":{}}},
   *     summary="Get available payment means",
   *     description="Fetch a list of available payment means from FIRS.",
   *     @OA\Response(
   *         response=200,
   *         description="List of payment means successfully retrieved",
   *         @OA\JsonContent(type="object")
   *     ),
   *     @OA\Response(
   *         response=500,
   *         description="Failed to fetch payment means"
   *     )
   * )
   */
  public function getPaymentMeans()
  {
    try {
      $response = $this->firsApiService->getPaymentMeans();
      return response()->json($response, 200);
    } catch (\Throwable $e) {
      Log::error('FIRS GetPaymentMeans Error: ' . $e->getMessage());
      return response()->json(['message' => 'Failed to fetch payment means.'], 500);
    }
  }

  /**
   * @OA\Get(
   *     path="/api/v1/resources/tax-categories",
   *     tags={"Resources"},
   *     security={{"sanctum":{}}},
   *     summary="Get available tax categories",
   *     description="Fetch all tax categories available in FIRS.",
   *     @OA\Response(
   *         response=200,
   *         description="List of tax categories successfully retrieved",
   *         @OA\JsonContent(type="object")
   *     ),
   *     @OA\Response(
   *         response=500,
   *         description="Failed to fetch tax categories"
   *     )
   * )
   */
  public function getTaxCategories()
  {
    try {
      $response = $this->firsApiService->getTaxCategories();
      return response()->json($response, 200);
    } catch (\Throwable $e) {
      Log::error('FIRS GetTaxCategories Error: ' . $e->getMessage());
      return response()->json(['message' => 'Failed to fetch tax categories.'], 500);
    }
  }

  /**
   * @OA\Get(
   *     path="/api/v1/resources/tin/{tin_number}",
   *     tags={"Resources"},
   *     security={{"sanctum":{}}},
   *     summary="Lookup taxpayer by TIN",
   *     description="Retrieve taxpayer details by Tax Identification Number (TIN).",
   *     @OA\Parameter(
   *         name="tin_number",
   *         in="path",
   *         required=true,
   *         description="Taxpayer Identification Number",
   *         @OA\Schema(type="string", example="12345678-0001")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Taxpayer details successfully retrieved",
   *         @OA\JsonContent(type="object")
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="TIN not found"
   *     ),
   *     @OA\Response(
   *         response=500,
   *         description="Failed to fetch TIN details"
   *     )
   * )
   */
  public function getTin(string $tin_number)
  {
    try {
      $response = $this->firsApiService->getTin($tin_number);
      return response()->json($response, 200);
    } catch (\Throwable $e) {
      Log::error("FIRS GetTIN Error for {$tin_number}: " . $e->getMessage());
      return response()->json(['message' => 'Failed to fetch TIN details.'], 500);
    }
  }

  /**
   * @OA\Get(
   *     path="/api/v1/resources/entity/{entity_id}",
   *     tags={"Resources"},
   *     security={{"sanctum":{}}},
   *     summary="Retrieve entity details by ID",
   *     description="Fetch entity details using its unique identifier.",
   *     @OA\Parameter(
   *         name="entity_id",
   *         in="path",
   *         required=true,
   *         description="The FIRS entity ID",
   *         @OA\Schema(type="string", example="abc123xyz")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Entity details successfully retrieved",
   *         @OA\JsonContent(type="object")
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Entity not found"
   *     ),
   *     @OA\Response(
   *         response=500,
   *         description="Failed to fetch entity details"
   *     )
   * )
   */
  public function getEntity(string $entity_id)
  {
    try {
      $response = $this->firsApiService->getEntity($entity_id);
      return response()->json($response, 200);
    } catch (\Throwable $e) {
      Log::error("FIRS GetEntity Error for {$entity_id}: " . $e->getMessage());
      return response()->json(['message' => 'Failed to fetch entity details.'], 500);
    }
  }
}
