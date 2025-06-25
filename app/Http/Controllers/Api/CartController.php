<?php


namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;

class CartController extends ApiController
{

    // protected function getUserCart()
    // {
    //     // Fetch or create the user's cart
    //     $cart = request()->user()->cart()->firstOrCreate([]);
    
    //     // Load relationships, including seller business coordinates and products with their prices
    //     $cart->load([
    //         'products.images', 
    //         'user' => function ($query) {
    //             $query->select('id', 'mobile_number'); // Adjust if needed
    //         },
    //         'cartItems.seller' => function ($query) {
    //             $query->select('user_id', 'business_coordinates'); // Adjust if needed
    //         },
    //         'digitalAddresses'
    //     ]);
    
    //     // Transform business_coordinates into an array if it's a string
    //     $cart->cartItems->each(function ($item) {
    //         if ($item->seller && is_string($item->seller->business_coordinates)) {
    //             // Convert the business_coordinates string into an array
    //             $item->seller->business_coordinates = array_map('trim', explode(',', $item->seller->business_coordinates));
    //         }
    //     });
    
    //     // Add the referral code and product price to each cartItem
    //     $cart->cartItems->each(function ($cartItem) {
    //         // Find the corresponding product for the current cartItem
    //         $product = $cartItem->product;
    
    //         // If a product exists, add the referral code and price to the cartItem
    //         if ($product) {
    //             $cartItem->referralCode = $cartItem->referral_code; // Add referral code
    //             $cartItem->product_price = $product->price; // Add product price to the cartItem
    //         }
    //     });
    
    //     // Add the referral code to each product in the cart
    //     $cart->products->each(function ($product) use ($cart) {
    //         // Find the corresponding cartItem for the current product
    //         $cartItem = $cart->cartItems->firstWhere('product_id', $product->id);

            
    //         // If a cartItem exists, add the referral code to the product
    //         if ($cartItem) {
    //             $product->referralCode = $cartItem->referral_code;
    //         }
    //     });
    
    //     // Return the cart with transformed business_coordinates, referral codes, and prices in cartItems
    //     return $cart;
    // }

    protected function getUserCart()
{
    // Fetch or create the user's cart
    $cart = request()->user()->cart()->firstOrCreate([]);

    // Load relationships, including seller business coordinates, and cart items
    $cart->load([
        'products.images',
        'user' => function ($query) {
            $query->select('id', 'mobile_number'); // Adjust if needed
        },
        'cartItems.seller' => function ($query) {
            $query->select('user_id', 'business_coordinates'); // Adjust if needed
        },
        'digitalAddresses'
    ]);

    // Directly fetch cart items and join with the products table to get the price
    $cartItemsWithPrice = \DB::table('cart_items')
        ->join('products', 'cart_items.product_id', '=', 'products.id')  // Join cart_items with products
        ->where('cart_items.cart_id', $cart->id)  // Only fetch items from the current cart
        ->select(
            'cart_items.*',
            'products.price as product_price'  // Select the price from the products table
        )
        ->get();

    // Add the product price to each cartItem
    $cart->cartItems->each(function ($cartItem) use ($cartItemsWithPrice) {
        // Find the corresponding cartItem with the price
        $cartItemData = $cartItemsWithPrice->firstWhere('id', $cartItem->id);
        if ($cartItemData) {
            // Add the price to the cartItem
            $cartItem->product_price = $cartItemData->product_price;
        }

        // Transform business_coordinates into an array if it's a string
        if ($cartItem->seller && is_string($cartItem->seller->business_coordinates)) {
            $cartItem->seller->business_coordinates = array_map('trim', explode(',', $cartItem->seller->business_coordinates));
        }
    });

    // Return the cart with updated cartItems, now including product price
    return $cart;
}

    
    
    
    

    function getCart()
    {
        return $this->genericSuccess($this->getUserCart());
    }


    function addDigitalAddress(Request $request)
    {
        $request->validate([
            'digital_address_id' => 'required|exists:digital_addresses,id',
        ]);

        $user = $request->user();
        request()->user()->cart()->update(["digital_address_id" => $request->input('digital_address_id')]);

        return $this->success('Product successfully added.', [
            'cart' => $this->getUserCart()
        ]);
        //return $this->genericSuccess($user->cart->with('cartItems')->get());

    }

    function addCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'seller_id' => 'required|exists:users,id',
            'quantity' => 'required|numeric|min:1|max:999999'
        ]);

        $user = $request->user();
        // dd($user);

        $cart = $user->cart()->firstOrCreate([]);
      
          // Check if the cart creation or retrieval failed
            if (!$cart) {
                // Return error if no cart was found or created
                return $this->error('Unable to create or retrieve cart.', 400);
            }

        // Sync the product with quantity and seller_id without detaching existing items

       $cart->products()->syncWithoutDetaching([
        $request->input('product_id') => [
            'quantity' => $request->input('quantity'),
            'seller_id' => $request->input('seller_id') // Include seller_id
        ]
        ]);

        return $this->success('Product successfully added.', [
            'cart' => $this->getUserCart()
        ]);
        //return $this->genericSuccess($user->cart->with('cartItems')->get());

    }

    function updateCart(Request $request)
    {

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1|max:999999',
        ]);

        $user = $request->user();

        $product = $user->cart->products()->find($request->input('product_id'));

        if ($product) {
            $product->pivot->quantity = $request->input('quantity');
            $product->pivot->save();
        }

        return $this->success('Product successfully updated.', [
            'cart' => $this->getUserCart()
        ]);
    }

    function deleteItemFromCart(Request $request)
    {

        $user = $request->user();

        //$user->cart->products()->where(['product_id'=>$request->product_id])->delete();
        $user->cart->products()->detach($request->input('product_id'));
        return $this->success('Product successfully deleted.', [
            'cart' => $this->getUserCart()
        ]);
        //return $this->genericSuccess($user->cart->with('cartItems')->get());

    }
}
