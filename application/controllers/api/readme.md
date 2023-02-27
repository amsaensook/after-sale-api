## Structure API

step code structure API

1. form_validation ( case : receive input parameter)
2. Get Data from Database 
3. generateToken (case : first login)
4. validateToken (case : every call API for check status login and check permission use menu)

code check token

- print_r($this->authorization_token->userData());
- print_r($this->authorization_token->validateToken());

code CORS (use from file rest.php in folder config)

- header('Access-Control-Allow-Origin: *'); - add to method (optional)




  