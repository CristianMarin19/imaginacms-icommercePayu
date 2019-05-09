# asgardcms-icommercepayu

### Add Except

1. Go to app/http/middleware/VerifyCsrfToken
2. add this:  

	protected $except = [
        '/icommercepayu/ok'
    ];

### Data Configuration Example

- merchantId: 	
	508029

- apilogin:
	pRRXKOl8ikMmt9u	
	
- apiKey:
	4Vj8eK4rloUd272L48hsrarnUA

- accountId:
	512321

-  currency:
	COP

-   Allow use of Test Credit Cards
	