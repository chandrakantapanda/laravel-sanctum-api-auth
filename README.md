# Laravel8 REST API with Sanctum
This is an example of a REST API using auth tokens with Laravel Sanctum

### You have to just follow a few steps to get following web services
##### Login API

##### Product API


## Getting Started
### Step 1: setup database in .env file

```` 
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD= 
````

## Step 2:Create a schema for product table.

````javascript
php artisan make:migration create_products_table

````

## Step 3:Create a schema for product table.

````javascript
../database/migrations/create_products_table.php
... 
	public function up(){
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('detail');
            $table->timestamps();
        });
    }


````

## Step 4:Run your database migrations.

````javascript
php artisan migrate

````

## Step 5:Add the Sanctum's middleware.

````
../app/Http/Kernel.php

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

...

    protected $middlewareGroups = [
        ...

        'api' => [
            EnsureFrontendRequestsAreStateful::class,
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    ...
],

````


## Step 6:Let's create the seeder for the User model

```javascript 
php artisan make:seeder UsersTableSeeder
````

## Step 7:Now let's insert as record

```javascript 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
...
...
DB::table('users')->insert([
    'name' => 'John Doe',
    'email' => 'john@doe.com',
    'password' => Hash::make('password')
]);
````

## Step 8:To seed users table with user

```javascript 
php artisan db:seed --class=UsersTableSeeder
````


## Step 9:  create a controller 
```javascript 
	php artisan make:controller API/AuthController
	php artisan make:controller API/ProductController
````

## Step 10:  add these in AuthController  and  /login route in the routes/api.php file:


```javascript
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends Controller {
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
            'c_password' => 'required|same:password',
        ]);
        if($validator->fails()){
            $response = [
                'success' => false,
                'message' => 'Validation Error.',
                'data'    => $validator->errors(),
            ];
            return response()->json($response, 404);    
        }
        $fields = $request->all();
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'success' => true,
            'message' => 'User register successfully.',
            'data'    => $user,
        ];
        
        return response()->json($response, 200);
    }
    function login(Request $request){
        $user= User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            $response = [
                'success' => false,
                'message' => 'Unauthorised.',
            ];
            return response()->json($response, 404);
        }
    
        $result['token'] =  $user->createToken('my-app-token')->plainTextToken;
        $result['name']  =   $user->name;   
        $result['email']  =   $user->email;   
        $response = [
            'success' => true,
            'message' => 'User login successfully.',
            'data'    => $result,
        ];
        
        return response()->json($response, 200);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();
        $response = [
                'success' => true,
                'message' => 'User Logged Out.',
                'data'    => array(),
            ];        
        return response()->json($response, 200);
    }
}



````


## Step 11: Test with postman, Result will be below

```javascript 

{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@doe.com",
        "email_verified_at": null,
        "created_at": null,
        "updated_at": null
    },
    "token": "AbQzDgXa..."
}

````

## Step 11: Make Details API or any other with secure route  

```javascript 

Route::group(['middleware' => 'auth:sanctum'], function(){
    //All secure URL's

    });


Route::post("login",[UserController::class,'index']);

````


## Routes with postman

```
# Public

GET   /api/products
GET   /api/product/:id

POST   /api/login
@body: email, password

POST   /api/register
@body: name, email, password, c_password


# Protected

POST   /api/product
@body: name, slug, description, price

PUT   /api/product/:id
@body: name, slug, description, price

DELETE  /api/product/:id

POST    /api/logout
```