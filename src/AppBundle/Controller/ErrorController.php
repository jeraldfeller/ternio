<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 1/30/2018
 * Time: 1:33 AM
 */

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller
{
    /**
     * @Route("/error")
     */
    public function renderLoginForm()
    {
        return $this->render(
            '/error/error.html.twig'
        );
    }

}