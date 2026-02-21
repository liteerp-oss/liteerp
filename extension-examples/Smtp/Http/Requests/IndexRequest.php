<?php 
namespace Extensions\Smtp\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest{
    public function authorize() : bool{
        return true;
    }
    public function rules(): array{
        return [];
    }
}