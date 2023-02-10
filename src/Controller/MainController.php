<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController
{
    #[Route('/')]
    public function mainAction():Response
    {
        return new Response('<html><body>Головна сторінка</body></html>');

}
}