<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json(['status' => 404, 'msg' => 'Token inválido']);
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => 404, 'msg' => 'Token expirado']);
        } catch (JWTException $e) {
            return response()->json(['status' => 404, 'msg' => 'Token de autorização não encontrado']);
        }
        return $next($request);
    }
}
