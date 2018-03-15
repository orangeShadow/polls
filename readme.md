# Polls package for Laravel

## Install 

```composer require orangeshadow/polls```

then copy config, migration and lang
 
```php artisan vendor:publish OrangeShadow\\Polls\\SeriviceProvider``` 

run migration

```php artisan migrate```


## Config

polls.php

```admin_route_prefix``` - prefix for manage polls API route

```admin_route_middleware```  - array middleware for manage polls route

```public_route_prefix``` - prefix for public API route

```public_route_middleware```  - array for manage polls route

```paginate``` - count items returned from api    


## Routes

admin route:
       
    GET: /poll - Get poll list
    
    POST: /poll - Store Poll  
      
    GET: /poll/{poll} - Show poll
               
    PUT: /poll/{poll} - Update poll
                  
    DELETE: /poll/{poll} - Delete poll
    
    POST: /poll/{poll}/close - Close Poll

    GET: /option - Get option list
    
    POST: /option - Store Option
    
    GET: /option/{option} - Show option
    
    PUT: /option/{option} - Update option
    
    DELETE: /option/{option}  Remoce option
 
   
public vote (only for auth user)
 
    POST: poll/{poll}/vote
    FORM-PARAMS [
        options: array 
    ]
   
   
    
## Facade PollProxy
    
    
    $pollProxy = app('PollPropxy',['poll'=>$poll])
    
Save Vote:
    
    $pollProxy->voting($user_id,$options);
    
Get Result Array:
    
    $pollProxy->getResult();
    
    