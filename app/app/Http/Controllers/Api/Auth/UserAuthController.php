<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Auth\User\StepTwoLoginRequest;
use App\Classes\Otp\OtpFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\StepOneLoginRequest;
use App\Jobs\SendOtpCodeJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Mockery\Exception;

class UserAuthController extends Controller
{


    /**
     * @param StepOneLoginRequest $request
     * @return mixed
     */
    public function stepOne( StepOneLoginRequest $request) : mixed
    {
        if (!$this->createUser($request->type, $request->value)) {
            throw new Exception();
        }

        //generate random code
        $code = Str::random(6);

        //save code to cache for 2 min
        Cache::put($request->value,
                   ['type' => $request->type, 'code' => $code],
                   Carbon::now()->addMinutes(2));


        dispatch(
            new SendOtpCodeJob(
                $request->type,
                $request->value,
                $code
            )
        );

        return  [
//            'code' => $code //IMPORTANT code dont return in response in product stage
        ];
    }


    /**
     * @param StepTwoLoginRequest $request
     * @return array
     */
    public function stepTwo( StepTwoLoginRequest $request) : array
    {
        $cache = Cache::get($request->value);
        if (!$cache) {
            throw new Exception('not valid');
        }
        if($cache['code'] != $request->code) {
            throw new Exception('code not valid');
        }

        $user = User::where($this->getRowFromRequest($cache['type']), $request->value)->first();
        if (!$user) {
            throw new Exception('user not found!');
        }


        return [
            'token' => $user->createToken('authToken')->plainTextToken
        ];



    }




    /**
     * @param string $type
     * @param string $value
     * @return mixed
     */
    private function createUser( string $type, string $value) : User
    {
        return User::firstOrCreate([
           $this->getRowFromRequest($type) => $value
        ]);
    }


    /**
     * @param string $type
     * @return string
     */
    private function getRowFromRequest( string $type) : string
    {
        return match($type) {
            'sms' => 'mobile',
            'email' => 'email',
        };
    }




}
