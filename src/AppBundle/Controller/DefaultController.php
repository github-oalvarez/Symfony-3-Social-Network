<?php
// namespace BackendBundle\Form;

/* Cambiamos el namespace al cambiar el Bundle                     *********************************/
namespace AppBundle\Controller;
/***************************************************************************************************/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/***************************************************************************************************/
class DefaultController extends Controller
{
/* MÉTODO INDEX ************************************************************************************/
  public function indexAction(Request $request)
  {
    // indicamos la vista
    return $this->render('AppBundle::index.html.twig');
  }
/***************************************************************************************************/
}
