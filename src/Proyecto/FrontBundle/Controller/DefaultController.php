<?php

namespace Proyecto\FrontBundle\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Proyecto\PrincipalBundle\Entity\Data;
use Proyecto\PrincipalBundle\Entity\Categoria;
use Proyecto\PrincipalBundle\Entity\Entrada;
use Proyecto\PrincipalBundle\Entity\Usuario;
use Proyecto\PrincipalBundle\Entity\CmsReservation;

class DefaultController extends Controller {
	public function inicioAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('inicio', $this);
		$secondArray = array();
		$secondArray['backgrounds'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> findByHome(1);

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default:inicio.html.twig', $array);
	}
	public function biografiaAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('biografia', $this);
		$secondArray = array();
		$secondArray['page'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find(3);
		$secondArray['resource'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getBackground());
		$secondArray['media'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getMedia());

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default:biografia.html.twig', $array);
	}
	public function noticiasAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('noticias', $this);
		$secondArray = array();
		$secondArray['articles'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> findByType(0);
		$secondArray['media'] = array();
		
		for($i=0;$i<count($secondArray['articles']);$i++){
			$secondArray['media'][$i] =  $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['articles'][$i]->getMedia()); 
		}
		$secondArray['page'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find(3);
		
		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default:noticias.html.twig', $array);
	}
	public function articleAction($id) {
		$firstArray = UtilitiesAPI::getDefaultContent('noticias', $this);
		$secondArray = array();
		$secondArray['article'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> find($id);
		
		$secondArray['resource'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['article']->getBackground());
		$secondArray['media'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['article']->getMedia());
		
		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default:article.html.twig', $array);
	}
	public function programacionAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('programacion', $this);
		$secondArray = array();
		$secondArray['articles'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> findByType(1);
		$secondArray['media'] = array();
		
		for($i=0;$i<count($secondArray['articles']);$i++){
			$secondArray['media'][$i] =  $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['articles'][$i]->getMedia()); 
			$secondArray['dates'][$i] =  $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsDate') -> findByArticle($secondArray['articles'][$i]->getId()); 
		}
		$secondArray['page'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find(2);
		$secondArray['background'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getBackground());
		
		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default:programacion.html.twig', $array);
	}
	public function talleresAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('talleres', $this);
		$secondArray = array();
		$secondArray['articles'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> findByType(2);
		$secondArray['media'] = array();
		
		for($i=0;$i<count($secondArray['articles']);$i++){
			$secondArray['media'][$i] =  $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['articles'][$i]->getMedia()); 
			$secondArray['dates'][$i] =  $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsDate') -> findByArticle($secondArray['articles'][$i]->getId()); 
		}
		$secondArray['page'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find(7);
		$secondArray['background'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getBackground());
		
		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default:talleres.html.twig', $array);
	}
	
	public function gastrobarAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('gastrobarla', $this);
		$secondArray = array();
		$secondArray['page'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find(33);
		
		$secondArray['background'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getBackground());
		$secondArray['media'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getMedia());
		$secondArray['theme'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> find($secondArray['page']->getTheme());

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default:gastrobar.html.twig', $array);
	}
	public function contactoAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('contacto', $this);
		$secondArray = array();
		$secondArray['page'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find(14);
		$secondArray['background'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getBackground());
		$secondArray['theme'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> find($secondArray['page']->getTheme());

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default:contacto.html.twig', $array);
	}
	
	public function reservationAction($id, Request $request) {
		$firstArray = UtilitiesAPI::getDefaultContent('reservation', $this);
		$secondArray = array();
		$secondArray['article'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> find($id);
		
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$data = new CmsReservation();
		$form = $this -> createFormBuilder($data) 
		-> add('name', 'text', array('required' => false))
		-> add('lastName', 'text', array('required' => false)) 
		-> add('phone', 'text', array('required' => false)) 
		-> add('email', 'text', array('required' => false)) 
		-> getForm();

		$em = $this -> getDoctrine() -> getEntityManager();
		$secondArray['message'] = '';

		if ($this -> getRequest() -> isMethod('POST')) {
			$form -> bind($this -> getRequest());
			
			$data -> setDate(new \DateTime());
			$data -> setChecked(false);
			$data -> setArticle($secondArray['article']);
			
			$em -> persist($data);
			$em -> flush();
			
			$secondArray['message'] = 'Estimado(a) '.ucfirst($data -> getName()).' '.ucfirst($data -> getLastName()).' su reservación ha sido guardada exitosamente...';
			
														}
		
		$secondArray['form'] = $form -> createView();
		$secondArray['url'] =  $this -> generateUrl('proyecto_front_reservation', array('id' => $id));
		$secondArray['theme'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> find(6)->getColor();
		$secondArray['background'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find(50)->getWebPath();
		

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default:reservation.html.twig', $array);
	}

}
