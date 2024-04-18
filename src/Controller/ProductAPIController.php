<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'app_')]
class ProductAPIController extends AbstractController
{
    #[Route('/products', name: 'app_product_api')]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        return $this->json([
            "message" => "List of all products",
            "data" => $products,
            "status" => JsonResponse::HTTP_OK
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $product = new Product();

        $name = $request->request->get("name");

        if (strlen($name) == 0) {
            return $this->json([
                "message" => "Name can't be empty",
                "status" => JsonResponse::HTTP_BAD_REQUEST
            ]);
        }

        $price = $request->request->get("price");

        if (strlen($price) == 0) {
            return $this->json([
                "message" => "price can't be empty",
                "status" => JsonResponse::HTTP_BAD_REQUEST
            ]);
        }

        $product->setName($name);
        $product->setPrice($price);

        $entityManager->persist($product);
        $entityManager->flush();



        return $this->json([
            "message" => "new product has been created!",
            "data" => $product,
            "status" => JsonResponse::HTTP_CREATED
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): JsonResponse
    {

        return $this->json([
            "message" => "Product details!",
            "data" => $product,
            "status" => JsonResponse::HTTP_OK
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Product $product): JsonResponse
    {
        // taking values from the request 
        $name = $request->request->get("name");
        $price = $request->request->get("price");
        // setting values inside the product object
        $product->setName($name);
        $product->setPrice($price);
        // prepare and run the query to update
        $entityManager->persist($product);
        $entityManager->flush();


        // response in JSON to the user
        return $this->json([
            "message" => "product has been updated!",
            "data" => $product,
            "status" => JsonResponse::HTTP_OK
        ]);
    }

    #[Route('/{id}/remove', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Product $product, EntityManagerInterface $entityManager): JsonResponse
    {

        $entityManager->remove($product);
        $entityManager->flush();


        // response in JSON to the user
        return $this->json([
            "message" => "product has been deleted!",
            "status" => JsonResponse::HTTP_OK
        ]);
    }
}
