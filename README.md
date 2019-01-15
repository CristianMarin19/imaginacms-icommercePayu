# asgardcms-icommercepayu

## Seeder

    run php artisan module:seed Icommercepayu

## Add Except

    1. Go to app/http/middleware/VerifyCsrfToken
    2. add this:
        protected $except = [ '/icommercepayu/ok' ];

# Configurations

    - merchantId: 508029
    - apilogin: pRRXKOl8ikMmt9u
    - apiKey: 4Vj8eK4rloUd272L48hsrarnUA
    - accountId: 512321
    - Allow use of Test Credit Cards

## API

### Init (Parameters = orderID)
    
    https://icommerce.imagina.com.co/api/icommercepayu/{orderid}




