<?php
namespace Mahindra\Cc_auth\Controllers;

use Illuminate\Http\Request;
use Mahindra\Cc_auth\Classes\ValidationService;

class AuthenticationController
{

    private $validationService;
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function checkValidation(Request $request)
    {
        $reqData = $request->IAM;
        dd($reqData['user']);
    }
}