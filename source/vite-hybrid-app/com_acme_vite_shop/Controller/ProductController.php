<?php
namespace App\com_acme_vite_shop\Controller;

use Pinoox\Component\Helpers\PinooxScriptHelper;
use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class ProductController extends Controller
{
    public function show(int $id = 1)
    {
        $products = [
            1 => ['id' => 1, 'title' => 'ماگ پینوکس', 'summary' => 'ماگ سرامیکی با لوگوی پینوکس', 'unit_price' => 89000],
            2 => ['id' => 2, 'title' => 'تی‌شرت توسعه‌دهنده', 'summary' => 'نخ پنبه‌ای، دوخت محکم', 'unit_price' => 245000],
        ];

        $product = $products[$id] ?? $products[1];

        View::shareSeo([
            'title' => $product['title'] . ' | فروشگاه نمونه',
            'description' => $product['summary'],
        ]);

        return View::render('pages/product.twig', [
            'product' => $product,
            'bootstrap' => PinooxScriptHelper::bootstrap([
                'productId' => $product['id'],
                'unitPrice' => $product['unit_price'],
                'currency' => 'تومان',
            ]),
        ]);
    }
}
