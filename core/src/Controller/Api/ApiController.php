<?php

namespace App\Controller\Api;

use App\Traits\ApiRequestValidation;
use App\Traits\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    use ApiResponse, ApiRequestValidation;
}
