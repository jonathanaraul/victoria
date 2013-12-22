<?php

namespace Proyecto\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Proyecto\PrincipalBundle\Entity\User;
use Proyecto\PrincipalBundle\Entity\Data;
use Proyecto\PrincipalBundle\Entity\Categoria;
use Proyecto\PrincipalBundle\Entity\CmsPage;




class HelpersController extends Controller
{
    public function menuAction($idpage,$theme)
    {
    	$locale = UtilitiesAPI::getLocale($this);
	
		$em = $this->getDoctrine()->getManager();
		
		$query = $em -> createQuery('SELECT d
    								 FROM ProyectoPrincipalBundle:CmsPage d
   	 								 WHERE d.lang      = :locale and
   	 								       d.published = :published
    								 ORDER BY d.rank ASC') 
    		   -> setParameter('locale', $locale)
			   -> setParameter('published', 1);

		$array['objects'] = $query -> getResult();
		$array['idpage'] = $idpage;
		$array['theme'] = $theme;
	
        return $this->render('ProyectoFrontBundle:Helpers:menu.html.twig', $array);
    }
    public function ultimosArticulosAction($tipo,$titulo)
    {
    	$array = array('titulo'=>$titulo );
	
		$em = $this->getDoctrine()->getManager();
		
		$query = $em -> createQuery('SELECT d
    								 FROM ProyectoPrincipalBundle:Data d,
    	 								  ProyectoPrincipalBundle:Categoria c
   	 								 WHERE d.categoria = c.id AND
   	 								       c.tipo = :tipo
    								 ORDER BY d.fecha DESC') -> setParameter('tipo', $tipo);

		$array['objects'] = $query -> getResult();
		
	
        return $this->render('ProyectoFrontBundle:Helpers:ultimosarticulos.html.twig', $array);
    }
    public function galeriaAction($tipo,$titulo)
    {
    	$array = array('titulo'=>$titulo );
	
		$em = $this->getDoctrine()->getManager();
		
		$query = $em -> createQuery('SELECT d
    								 FROM ProyectoPrincipalBundle:Data d,
    	 								  ProyectoPrincipalBundle:Categoria c
   	 								 WHERE d.categoria = c.id AND
   	 								       c.titulo = :tipo
    								 ORDER BY d.fecha ASC') -> setParameter('tipo', $tipo);

		$array['objects'] = $query -> getResult();
		
	
        return $this->render('ProyectoFrontBundle:Helpers:galeria.html.twig', $array);
    }

    public function slideshowAction()
    {
			
		$em = $this->getDoctrine()->getManager();
		$query = $em -> createQuery('SELECT d
    								 FROM ProyectoPrincipalBundle:Data d,
    	 								  ProyectoPrincipalBundle:Categoria c
   	 								 WHERE d.categoria = c.id AND
   	 								       c.titulo = :tipo
    								 ORDER BY d.rank DESC') -> setParameter('tipo', 'slideshow');

		$secondArray['objects'] = $query -> getResult();
		
        return $this->render('ProyectoFrontBundle:Helpers:slideshow.html.twig', $secondArray);
    }

}
