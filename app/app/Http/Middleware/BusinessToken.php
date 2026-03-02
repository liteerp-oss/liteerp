<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use Carbon\Carbon;
use Closure;
use Core\AppToken\Application\UseCases\ParseAppToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;
class BusinessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function __construct(private ParseAppToken $parseAppToken)
    {
        
    }
    public function handle(Request $request, 
        Closure $next): Response
    {
        if (!$request->header('business-access')) {
            throw new UnauthorizedException(__("You has been verified business to failed"));
        }

        try {
            $businessInfo = $this->parseAppToken->handle($request->header('business-access'));
        } catch (InvalidArgumentException $e) {
            throw new UnauthorizedException(__("You has been verified business to failed"));
        } catch (DomainException $e) {
            throw new UnauthorizedException(__("You has been verified business to failed"));
        } catch (SignatureInvalidException $e) {
            throw new UnauthorizedException(__("You has been verified business to failed"));
        } catch (BeforeValidException $e) {
            throw new UnauthorizedException(__("You has been verified business to failed"));
        } catch (ExpiredException $e) {
            throw new UnauthorizedException(__("You has not logged"));
        } catch (UnexpectedValueException $e) {
            throw new UnauthorizedException(__("You has been verified business to failed"));
        }
        /**
         * Merge 
         */
        $request->merge([
            'business_id' => $businessInfo->data->id,
            'user_id' => $businessInfo->data->user_id,
            'username' => $businessInfo->data->username
        ]);
        return $next($request);
    }
}
