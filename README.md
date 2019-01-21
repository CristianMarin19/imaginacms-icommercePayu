# asgardcms-icommercepayu

## Seeder

    run php artisan module:seed Icommercepayu

## Add Except

    1. Go to app/http/middleware/VerifyCsrfToken
    2. add this:
        protected $except = [ 'api/icommercepayu/response' ];

## Configurations

    - merchantId
    - apilogin
    - apiKey
    - accountId
    - Allow use of Test Credit Cards

## API

### Init (Parameters = orderID)
    
    https://icommerce.imagina.com.co/api/icommercepayu




