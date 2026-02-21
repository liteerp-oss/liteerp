<?php 
namespace Extensions\Smtp\Http\Controllers;

use App\Http\Controllers\Controller;

class SmtpController extends Controller {
      public function index() {
           return view('SMTP::index');   
      }
}