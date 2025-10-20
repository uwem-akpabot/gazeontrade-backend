<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\FrontendController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SampleController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'isAPIAdmin'])->group(function(){
    
    Route::get('/checkingAuthenticated', function(){
        return response()->json(['message' => 'You are in', 'status'=>200], 200);
    });

    // CATEGORY
    Route::post('add-category', [CategoryController::class, 'store']);
    Route::get('category', [CategoryController::class, 'index']); 
    Route::get('edit-category/{slug}', [CategoryController::class, 'edit']); 
    Route::put('update-category/{slug}', [CategoryController::class, 'update']); 
    Route::delete('delete-category/{slug}', [CategoryController::class, 'destroy']);
    Route::get('category-detail/{slug}', [CategoryController::class, 'detail']);
    // populate category dropdown list for use
    Route::get('populate-categories', [CategoryController::class, 'populateCategories']); 
    Route::get('subcategories/{category_id}', [SubcategoryController::class, 'getByCategory']);

    // SUBCATEGORY
    Route::post('add-subcategory', [SubcategoryController::class, 'store']);
    Route::get('subcategory', [SubcategoryController::class, 'index']); 
    Route::get('edit-subcategory/{slug}', [SubcategoryController::class, 'edit']); 
    Route::put('update-subcategory/{slug}', [SubcategoryController::class, 'update']); 
    Route::delete('delete-subcategory/{slug}', [SubcategoryController::class, 'destroy']);
    Route::get('subcategory-detail/{slug}', [SubcategoryController::class, 'detail']);
    
    // PRODUCT
    Route::post('add-product', [ProductController::class, 'store']);
    Route::get('product', [ProductController::class, 'index']); 
    Route::get('edit-product/{slug}', [ProductController::class, 'edit']); 
    Route::put('update-product/{slug}', [ProductController::class, 'update']); 
    Route::delete('delete-product/{slug}', [ProductController::class, 'destroy']);
    Route::get('product-detail/{slug}', [ProductController::class, 'detail']);

    // ORDER
    Route::get('/admin/orders', [OrderController::class, 'index']); 
    Route::get('/admin/orders/{id}', [OrderController::class, 'orderDetail']);

    // DASHBOARD
    Route::get('/dashboard-stats', [DashboardController::class, 'stats']);

    // BANNER
    Route::get('edit-banner/{id}', [DashboardController::class, 'editBanner']); 
    Route::post('update-banner/{id}', [DashboardController::class, 'updateBanner']);

    // Newsletters
    Route::get('/newsletter', [FrontendController::class, 'getSubscribers']); 
    Route::post('/newsletter/send', [FrontendController::class, 'sendNewsletter']); 

    // sample
    Route::post('add-sample', [SampleController::class, 'store']);
    Route::get('sample', [SampleController::class, 'index']);
    Route::get('edit-sample/{slug}', [SampleController::class, 'edit']); 
    Route::put('update-sample/{slug}', [SampleController::class, 'update']); 
    Route::delete('delete-sample/{slug}', [SampleController::class, 'destroy']);
});

// PUBLIC FRONTEND/Home Collections Routes
Route::get('list-categories', [FrontendController::class, 'listCategories']); // homepage left menu category listing
Route::get('categories', [FrontendController::class, 'categories']); // collections page category listings
Route::get('categories-products/{slug}', [FrontendController::class, 'categoriesProducts']);
Route::get('featured-products', [FrontendController::class, 'featuredProducts']); // homepage featured products
Route::get('popular-products', [FrontendController::class, 'popularProducts']); // homepage popular products

Route::get('productdetail/{category_slug}/{product_slug}', [FrontendController::class, 'productDetail']);

// Homepage public
Route::post('/newsletter-subscribe', [FrontendController::class, 'subscribe']);

Route::get('banner', [DashboardController::class, 'banner']); 

// Cart Routes
Route::post('add-to-cart', [CartController::class, 'addtocart']);
Route::get('cart', [CartController::class, 'viewcart']);
Route::put('cart-updatequantity/{cart_id}/{scope}', [CartController::class, 'updatequantity']);
Route::delete('delete-cartitem/{cart_id}', [CartController::class, 'deleteCartItem']);

Route::post('place-order', [CheckoutController::class, 'placeorder']);    