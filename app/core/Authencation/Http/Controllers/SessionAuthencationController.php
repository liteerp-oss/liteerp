<?php

namespace Core\Authencation\Http\Controllers;

use App\Http\Controllers\Controller;
use Core\Authencation\Application\UseCases\LoginBySessionToken;
use Core\Authencation\Application\UseCases\LogoutSessionToken;
use Core\Authencation\Http\Requests\SessionAuthencationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SessionAuthencationController extends Controller {
    public function login(LoginBySessionToken $useCase,SessionAuthencationRequest $request){
        $useCase->handle($request->all());
        return redirect()->to(URL::to('/dashboard/business'));
    }
    public function logout(LogoutSessionToken $useCase,Request $request){
        $useCase->handle($request->all());
        return redirect()->to(URL::to("/dashboard/login"));
    }
}