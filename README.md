# TCX v3.0.0 - Authentication Module for Client-Server Transaction (One Way - Two Way).

### TCX is authentication module to secure API Server.

It's adapt OAuth2 scheme, but it use more simplify scheme which provide authentication for client-server without need to define scopes.

#### Dependencies
Laravel **>= 5.6**

#### How to Install
```
composer require verzth/tcx
```

#### How to Use
1. Add our ServiceProvider on your **config/app.php**.
    ```
    <?php
    
    return [
        ...
        ..
        .
        
        'providers' => [
            ...
            Verzth\TCX\TCXServiceProvider::class,
            ...
        ]
        
        .
        ..
        ...
    ]
    ```

2. Add TCX Middleware in your **app/Http/Kernel.php**.
    - In every request.
    ```
        $middleware = [
            ...
            \Verzth\TCX\Middleware\TCXMiddleware::class,
            ...
        ]
    ```
    or
    - In your API group middleware only. 
    ```
        $middlewareGroups = [
            ...
            'api' => [
                ...
                \Verzth\TCX\Middleware\TCXMiddleware::class,
                ...
            ]
            ...
        ]
    ```
    or
    - In your route middleware, and you can add manually on your route.
    ```
        $routeMiddleware = [
            ...
            'tcx' => \Verzth\TCX\Middleware\TCXMiddleware::class,
            ...
        ]
    ```

3. Publish our vendor with artisan command.
    ```
    php artisan vendor:publish --provider=Verzth\TCX\TCXServiceProvider
    ```
    
4. Migrate our TCX DB. After migrate it, you will get 3 tables (tcx_applications, tcx_accesses, tcx_mkas).
    ```
    php artisan migrate
    ```
    
5. We provide DB Seeder to produce sample data, just run code below to get the sample.
    ```
    php artisan db:seed --class=TCXApplicationsTableSeeder
    ```

#### Implementation
1. Authentication Type, TCX support three ways authentication type:
    - **One Way Transaction Code (OWTC)**: Client only need to use **app_id** and **app_public** to access Server APIs.
    - **Two Way Transaction Code (TWTC)**: Client has to use **app_id** and **app_public** to get access token, then it can be used to access APIs.
    - **Free Transaction Code (FTC)**: Client use master token to access APIs, without need to request token for every requests, it's specially design to by pass **TWTC** authentication. You need to
      generate Master token manually.

    To specify authentication for each APIs assign parameter in the middleware, use **or (|)** sign to specify multiple authentication or use
    **all** to support all type (Supported type : **all**, **owtc**, **twtc**). By default, TCX will support all authentication if you didn't
    specifying supported type.

    Client need to specify type by sending **X-TCX-TYPE** header in every APIs request.

2. How to generate credentials:
    - **app_pass** or **X-TCX-APP-PASS**, it can be generate by hashing plain of joined token (param, time, or none), application public key,
      and client dynamic token with SHA1. Client need to append the given dynamic token to hash result by splitting with **colon (:)**
      symbol, then encrypt it with base64.
      - Param base
      
          Sample Parameter:
          ```
          abc=123
          _xyz=789
          foo=bar
          def=456
          bar=ghi
          ```
          Expected Token:
          ```
          _xyz=789&abc=123&bar=ghi&def=456&foo=bar
          ```
      - Time base
        
        ```
        tcx_datetime=YYYYMMDDHHmmss
        ```
        
        Sample
        ```
        tcx_datetime=20181230235959 // For 23:59:59, 30 December 2018
        ```
        
      - None, just using application password and client dynamic token.
    - **token** or **X-TCX-TOKEN**, it's provided when Client authorizing to server, but you need to encrypt it with base64.

3. Authentication Headers.
    - Type **OWTC**:
        - **'X-TCX-TYPE'**: **'OWTC'**.
        - **'X-TCX-APP-ID'**: Client ID.
        - **'X-TCX-APP-PASS'**: Client Password.
    - Type **TWTC**:
        - **'X-TCX-TYPE'**: **'TWTC'**.
        - **'X-TCX-APP-ID'**: Client ID.
        - **'X-TCX-APP-PASS'**: Client Password.
        - **'X-TCX-TOKEN'**: Access Token, obtained by doing authorization.
    - Type **FTC**:
        - **'X-TCX-TYPE'**: **'FTC'**.
        - **'X-TCX-APP-ID'**: Client ID.
        - **'X-TCX-APP-PASS'**: Client Password.
        - **'X-TCX-TOKEN'**: Master Access Token.

4. Authorization Routes, TCX provide some routes which is can be used to get access token in TWTC type:
    - **/tcx/authorize**:
        - METHOD: POST
        - Params:
            * **app_id**: Client application id.
            * **app_pass**: Client password.
        
        Sample **Fail** response
        
        ```
        {
            "status": 0,
            "status_number": "002",
            "status_code": "TCXAFX",
            "status_message": "Authentication Failed"
        }
        ```
        
        Sample **Success** response
        
        ```
        {
            "status": 1,
            "status_number": "701",
            "status_code": "TCXSSS",
            "status_message": "Authentication Success",
            "data": {
                "token": "tfDBOa6q3PPTJFd0A8HWftw2sXMV1b5ue6v0intK",
                "refresh": "fR0HLeL5qk0ZdtthI2ZsQLZx8BHEP2dSnVaQqkF5",
                "expired_at": "2018-10-16 13:31:43"
            }
        }
        ```
        
    - **/tcx/reauthorize**:
        
        Refresh token can be used to refresh your existing token, you can pass it to this service and your existing token
        will be extended. Service will reply new refresh token for your existing token to be used in next refresh.
        
        - METHOD: POST
        - Params:
            * **app_id**: Client application id.
            * **token**: Refresh Token.
            
        Sample **Fail** response
        
        ```
        {
            "status": 0,
            "status_number": "002",
            "status_code": "TCXAFX",
            "status_message": "Authentication Failed"
        }
        ```
        
        Sample **Success** response
        
        ```
        {
            "status": 1,
            "status_number": "701",
            "status_code": "TCXSSS",
            "status_message": "Token refreshed",
            "data": {
                "refresh": "04ITeVxWINOesyHH5Sxx57rN5uAW0ltCWN0cENxD",
                "expired_at": "2018-10-16 13:36:42"
            }
        }
        ```

5. Response Status.

    | Status | Number | Code | Message | Note |
    | :----: | :----: | :---: | ------- | -------- |
    | 0 | 70FF000 | TCXREQ | TCX Authentication Required | Provide Authentication Header |
    | 0 | 20FF001 | TCXRJC | TCX Authentication Rejected | X-TCX-Type not supported or disabled |
    | 0 | 40FF002 | TCXAFX | TCX Authentication Failed | X-TCX-App-ID not found, invalid, or inactive |
    | 0 | 50FF003 | TCXPFX | TCX Pass did not match | X-TCX-App-Pass not passed, crosscheck point 2 |
    | 0 | 50FF004 | TCXMKF | TCX Master Key did not valid | Check the master access key (Only FTC) |
    | 0 | 50FF005 | TCXTFX | TCX Token did not valid | Check the access key (Only TWTC) |
    
    Sample Response:
    ```
    {
        "status": 0,
        "status_code": "TCXREQ",
        "status_number": "70FF000",
        "status_message": "TCX Authentication Required"
    }
    ```