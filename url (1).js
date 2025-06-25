//const API_BASE = 'http://baymediasoft.be/sigma-loc8/public/api/v1';
export const BASE_DOMAIN = "http://3.23.33.189";
export const API_BASE = 'http://3.23.33.189/api/v1';
export const GOOGLE_PACES_API_BASE_URL = 'https://maps.googleapis.com/maps/api/place';

// Authentication API
export const API_REGISTER = `${API_BASE}/register`;
export const API_LOGIN = `${API_BASE}/login`;
export const API_VALIDATE_OTP = `${API_BASE}/mobile-verification`;
export const API_RESEND_OTP = `${API_BASE}/resend-otp`;
export const API_FORGOT_PASSWORD = `${API_BASE}/forgot-password`;
export const API_VALIDATE_PASSWORD_OTP = `${API_BASE}/check-otp`;
export const API_RESET_PASSWORD = `${API_BASE}/reset-password`;
export const API_LOGOUT = `${API_BASE}/logout`;

export const API_GET_CURRENT_USER = `${API_BASE}/me`;

// Digital Address
export const API_SEARCH_AREA = `${API_BASE}/digital-address/area_search`;
export const API_DIGITAL_ADDRESS = `${API_BASE}/digital-address`;

// Address Search
export const API_ADDRESS_SEARCH = `${API_BASE}/digital-address/search`;

// Account
export const API_PROFILE = `${API_BASE}/me`;
export const API_CHANGE_PASSWORD = `${API_BASE}/update-password`;

// Place
export const API_PLACE_CATEGORIES = `${API_BASE}/place/category`;
export const API_PLACES = `${API_BASE}/place`;

// Quiz
export const API_QUIZ_CATEGORIES = `${API_BASE}/knowledgebase/category`;
export const API_QUIZ_QUESTIONS = `${API_BASE}/knowledgebase`;

// Product
export const API_PRODUCT_CATEGORIES = `${API_BASE}/product/category`;
export const API_PRODUCTS = `${API_BASE}/product`;
export const API_SINGLE = `${API_BASE}/product/single`;

// Cart
export const API_CART_ADD_ITEM = `${API_BASE}/cart/addcart`;
export const API_CART_UPDATE_ITEM = `${API_BASE}/cart/updateCart`;
export const API_CART_REMOVE_ITEM = `${API_BASE}/cart/deleteItemFromCart`;
export const API_CART_LOAD = `${API_BASE}/cart/getCart`;
export const API_CART_ADD_ADDRESS = `${API_BASE}/cart/addDigitalAddress`;

// Order
export const API_PLACE_ORDER = `${API_BASE}/place-order`;

// Auto
export const API_AUTO_CATEGORIES = `${API_BASE}/auto/category`;
export const API_AUTOS = `${API_BASE}/auto`;
export const API_AUTO_SINGLE = `${API_BASE}/auto/single`;
export const API_AUTO_INTERESTED = `${API_BASE}/auto/setInterested`;
export const GET_AUTO_SELLERS = `${API_BASE}/get_autos_category_seller`;

// Real Estate
export const API_REAL_ESTATE_CATEGORIES = `${API_BASE}/realestate/category`;
export const API_REAL_ESTATES = `${API_BASE}/realestate`;
export const API_REAL_ESTATE_INTERESTED = `${API_BASE}/realestate/setInterested`;
export const API_REAL_ESTATE_SINGLE = `${API_BASE}/realestate/single`;
export const GET_REAL_ESTATE_SELLERS = `${API_BASE}/get_realestate_category_seller`;


//EDSA 
export const API_GET_EDSA_TRANSACTIONS = `${API_BASE}/get_edsa_transaction`;
export const API_SAVE_EDSA_TRANSACTION = `${API_BASE}/add/edsa-transaction`;

//SAVED Meters
export const API_SAVE_METER_NUMBER = `${API_BASE}/add/saved-meter`;
export const API_DELETE_METER_NUMBER = `${API_BASE}/delete/saved-meter`;
export const API_GET_SAVED_METERS = `${API_BASE}/saved-meters`;


//DSTV Meters
export const API_SAVE_DSTV_RECHARGE_NUMBER = `${API_BASE}/add/saved-recharge-cards`;
export const API_DELETE_DSTV_RECHARGE_NUMBER = `${API_BASE}/delete/saved-recharge-cards`;
export const API_GET_SAVED_DSTV_RECHARGE = `${API_BASE}/saved-dstv-recharge-cards`;
export const API_GET_DSTV_TRANSACTION = `${API_BASE}/get_dstv_transaction`;

//STAR Meters
export const API_SAVE_STAR_RECHARGE_NUMBER = `${API_BASE}/add/saved-star-recharge-cards`;
export const API_DELETE_STAR_RECHARGE_NUMBER = `${API_BASE}/delete/saved-star-recharge-cards`;
export const API_GET_SAVED_STAR_RECHARGE = `${API_BASE}/saved-star-recharge-cards`;
export const API_GET_STAR_TRANSACTIONS = `${API_BASE}/get_star_transaction`;



//edsa agent verification
export const API_GET_EDSA_OTP = `${API_BASE}/get-edsa-otp-verify`; 
export const API_VERIFY_EDSA_AGENT_OTP = `${API_BASE}/verify-edsa-otp`;
export const API_SET_EDSA_FIRST_TIME_PASSWORD = `${API_BASE}/set-edsa-password`;
export const API_VERIFY_EDSA_PASSWORD = `${API_BASE}/verify-edsa-password`;


//ads api
// shop ads, auto ads, realestate, utilities.
export const API_GET_ADS = `${API_BASE}/get-ads`;
export const API_GET_SHOP_ADS = `${API_BASE}/get-shop-ads`;
export const API_GET_AUTO_ADS = `${API_BASE}/get-auto-ads`;
export const API_GET_REALESTATE_ADS = `${API_BASE}/get-realestate-ads`;
export const API_GET_UTILITIES_ADS = `${API_BASE}/get-utilities-ads`;
export const API_GET_CATEGORY_SHOP_COMPANY = `${API_BASE}/seller-list-by-category`;



//aboutApp 
export const API_GET_ABOUT_APP = `${API_BASE}/aboutApp`;
//legal terms
export const API_GET_LEGAL = `${API_BASE}/legal`;
export const API_GET_PRIVACY_POLICY = `${API_BASE}/privacy`;
export const API_GET_INTELLECTUAL_PROPERTY = `${API_BASE}/intellectual`;
export const API_GET_COOKIES = `${API_BASE}/cookies`;
export const API_GET_PAYMENTS = `${API_BASE}/payments`;
export const API_GET_RETURNS = `${API_BASE}/returns`;
export const API_DSTV_RECHARGE = `${API_BASE}/dstv_recharge`;
export const API_GET_SUBSCRIPTION_TV = `https://sandbox.vtpass.com/api/service-variations?serviceID=`;
export const API_MERCHANT_VERIFY_TV = `https://sandbox.vtpass.com/api/merchant-verify`;
export const API_STAR_RECHARGE =`${API_BASE}/star_recharge`;
export const API_EDSA_RECHARGE =`${API_BASE}/edsa_api`;


////Air and ship
export const API_TRACK_NUMBER =`${API_BASE}/track_shipping`;
export const API_SUBMIT_AIR_SHIP =`${API_BASE}/add_air_sea_freight`;
export const API_GET_AIR_SHIP_COUNTRY =`${API_BASE}/shipper-by-country`;
export const API_GET_AIR_SHIP_SELLER_DETAILS =`${API_BASE}/get_seller`;

//// Collection and payment
export const API_GET_COLLECTION_PAYMENT_CUSTOMER_DETAILS =`${API_BASE}/get_payment_collection`;
export const GET_COLLECTION_PAYMENT_SELLER =`${API_BASE}/get_collection_seller`;

//// news 
export const API_GET_NEWS_SELLERS =`${API_BASE}/news/sellers`;
export const API_GET_STATE_NEWS =`${API_BASE}/state-news/seller`;
export const API_GET_NATIONAL_NEWS =`${API_BASE}/national-news/seller`;
export const API_GET_STATE_DETAILS_NEWS =`${API_BASE}/state-news`;
export const API_GET_NATIONAL_DETAILS_NEWS =`${API_BASE}/national-news`;
export const API_GET_NATIONAL_BRAKING_NEWS =`${API_BASE}/get_breaking_news`;


//// vehicles API
export const API_GET_VEHICLES =`${API_BASE}/get_chat_a_ride`;
export const API_GET_VEHICLES_BY_SELLER =`${API_BASE}/get_chat_a_ride_seller`;

//// Mobi-DOC API
export const API_GET_MOBI_DOC =`${API_BASE}/get_movie_doc`;
export const GET_MOBI_DOC_SELLERS =`${API_BASE}/get_movie_doc_seller`;


///// Money Transfer
export const API_GET_MONEY_TRANSFER =`${API_BASE}/get_money_transfer`;
export const API_GET_MONEY_TRANSFER_SELLERS =`${API_BASE}/get_money_transfer_seller`;

//// SERVICES API
export const API_GET_SERVICES_CATEGORY =`${API_BASE}/get_service_category`;
export const GET_SERVICES_SELLERS =`${API_BASE}/get_service_category_seller`;
