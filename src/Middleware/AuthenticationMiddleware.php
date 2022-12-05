<?php

namespace Mahindra\Cc_auth\Middleware;

use Closure;
use Mahindra\Cc_auth\Classes\ValidationService;
use App;

class AuthenticationMiddleware
{
    protected $appid;
    protected $env;
    protected $isMonolithic;
    protected $redirectUrl;
    protected $isAuthEnabled = false;
    protected $defaultUser = null;
    protected $validateURL;


    /**
     * Collect Environment Variable APP_ID , ENV ,IS_MONOLITH, IS_AUTH_ENABLED, DEFAULT_USER
     * DEFAULT_USER IS OPTIONAL
     * IF ANY ENVIRONMENT VARIABLE IS MISSED THROW ERROR
     */
    public function handle($request, Closure $next)
    {
        // APP_ID and its Exceptions
        $appid = env("APP_ID");
        if ($appid === null) {
            return response(['status' => 400, 'message' => 'environment variable APP_ID is required for Authentication SDK'], 400);
        }

        // ENV and its Exceptions
        $envString = env("ENV");
        if ($envString === null) {
            return response(['status' => 400, 'message' => 'environment variable ENV is required for Authentication SDK'], 400);
        }

        // Get IS_AUTH_ENABLED and its Exception
        $isAuthEnabledString = env("IS_AUTH_ENABLED");
        if ($isAuthEnabledString === null) {
            return response(['status' => 400, 'message' => 'environment variable IS_AUTH_ENABLED is required for Authentication SDK'], 400);
        }
        $isAuthEnabled = (boolean) $isAuthEnabledString;

        // IS_MONOLITH and its Exception
        $monolithicString = env("IS_MONOLITHIC");
        if ($monolithicString === null) {
            return response(['status' => 400, 'message' => 'environment variable IS_MONOLITHIC is required for Authentication SDK'], 400);
        }
        $monolithic = (boolean) $monolithicString;
        
        // Get DEFAULT_USER
        $defaultUser = env('DEFAULT_USER');
        
        // Redirect URL its require APP_ID,ENV
        $redirectUrl = "https://ccservice-dev.m-devsecops.com/auth/redirect?id=$appid&env=$envString";


        /**
         * If IS_AUTH_ENABLED == false then skip this filter
         * set default user as Current User
         * if default user is NULL then it will take root as a Current User
         */
        if(!$isAuthEnabled){
            return $next($request);
        }


        /**
         * Collect a cookie called jwt_{APP_ID}
         */
        $token = "jwt_$appid";


        /**
         * If JWT cookie not Null then Send its value to  Validate Function
         * If Response is 200 set Current User
         * Forward the filter chain to next filter or handler
         */
        if (isset($_COOKIE[$token])) {
            
            $jwtToken = $_COOKIE[$token];

            $ValidationService = new ValidationService;

            $httpResponse = $ValidationService->verifyJWT($jwtToken, $envString);
            $response = json_decode(curl_exec($httpResponse)); 
            $httpcode = curl_getinfo($httpResponse, CURLINFO_HTTP_CODE);
            if($httpcode==200){
                $response = curl_exec($httpResponse);
                $jsonArray = json_decode($response,true);
                $IAM = $jsonArray["data"];
                $request->request->add(['IAM'=>$IAM]);
                return $next($request);
            }
            curl_close($httpResponse);
        }

        /**
         * If the Application is Monolithic Application then Redirect its Login URL
         * OR Else Throw the Error  API Response
         */
        if($monolithic) return redirect($redirectUrl);
        return response(['status' => 401, 'error' => 'Token may be invalid or  expired', 'message' => 'Valid JWT token is required'], 401);
        
    }
}
