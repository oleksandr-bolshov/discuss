<?php

declare(strict_types=1);

namespace Apathy\Discuss\Validators;

use Illuminate\Support\Facades\Validator as SupportValidator;
use InvalidArgumentException;

abstract class Validator
{
    protected function validate($request, array $rules): void
    {
        $validator = SupportValidator::make(json_decode(json_encode($request), true), $rules);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
