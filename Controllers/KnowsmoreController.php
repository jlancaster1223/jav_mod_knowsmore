<?php

namespace Module\Knowsmore\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Libraries\Store\Product;

use App\Libraries\Media;

class KnowsmoreController extends BaseController
{
    public function search()
    {

        $suggestions = '';
        $categories = '';
        $products = '';

        $searchQuery = $this->request->getPost('q');
        // If it ends in an s, remove it
        if (substr($searchQuery, -1) == 's') {
            $searchQuery = substr($searchQuery, 0, -1);
        }

        // Select everything from categories where category_name is like the search term
        $categoryModel = new CategoryModel();
        $categories = $categoryModel
            ->like('category_name', $searchQuery)
            ->findAll(10);

        $categoriesReturn = '';
        if (!empty($categories)) {
            foreach ($categories as $category) {
                // Make the search term in the returned name bold
                if(str_replace($this->request->getPost('q'), '<span style="font-weight:600;">' . $this->request->getPost('q') . '</span>', $category['category_name'])){
                    $category['category_name'] = str_replace($this->request->getPost('q'), '<span style="font-weight:600;">' . $this->request->getPost('q') . '</span>', $category['category_name']);
                }
                $categoriesReturn .= '<a href="/category/' . $category['category_url'] . '">' . $category['category_name'] . '</a>';
            }
        }

        // Get first 4 products
        $productModel = new ProductModel();
        $products = $productModel
            ->like('name', $searchQuery)
            ->where('active', '1')
            ->findAll(4);

        $productsReturn = '';
        if(!empty($products)) {
            foreach ($products as $product) {
                $productObject = new Product();
                $productObject->setId($product['product_id']);
                $product = $productObject->getProduct();


                $link = '/'.$product['url'];
                $image = Media::getInlineHtmlAttributes($product['primary_image']['media_id']);
                $priceExcVat = number_format($product['price']['raw']['exc_tax'], 2);

                $productsReturn .= '
                <div class="products__item">
                    <figure class="m-0">
                        <a class="products__item__image" href="'. $link .'">
                            <img '. $image .'>
                        </a>
                        <button class="products__wishlist"  onclick="wishlists.add('. $product['product_id'] .')">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21.35L10.55 20.03C5.4 15.36 2 12.27 2 8.5C2 5.41 4.42 3 7.5 3C9.24 3 10.91 3.81 12 5.08C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.41 22 8.5C22 12.27 18.6 15.36 13.45 20.03L12 21.35Z"
                                        fill="#9FA4A6"/>
                            </svg>
                        </button>
                    </figure>
                    <a href="'. $link .'" class="products__title">'. $product['name'] .'</a>
                    <p>
                        '. $product['price']['display'] .' inc VAT<br>
                        <small style="opacity:0.6">£'. $priceExcVat .' exc vat</small>
                    </p>
                </div>';
            }
        }

        return json_encode([
            'suggestions' => (!empty($suggestions) ? $suggestions : 'No suggestions found'),
            'categories' => (!empty($categoriesReturn) ? $categoriesReturn : 'No categories found'),
            'products' => (!empty($productsReturn) ? $productsReturn : 'No products found'),
        ]);
    }
}