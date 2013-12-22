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
use Proyecto\PrincipalBundle\Entity\CmsPage;


class DefaultController extends Controller {
	public function indexAction() {
	 return $this->redirect($this->generateUrl('proyecto_front_homepage',array('_locale' => 'es')));
	}
	public function inicioAction() {

		$firstArray = UtilitiesAPI::getDefaultContent('inicio', $this);
		$secondArray = array();
		$secondArray['backgrounds'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> findByHome(1);
		$secondArray['idpage'] = null;
		$secondArray['theme'] = array('color'=>'black','id'=>0);
		
		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default:inicio.html.twig', $array);
	}
	public function pageAction($id,$friendlyname) {
		
		$firstArray = UtilitiesAPI::getDefaultContent('contacto', $this);
		$secondArray = array();
		$secondArray['page'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find($id);
		$secondArray['background'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getBackground());
		$secondArray['media'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getMedia());
		$secondArray['theme'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> find($secondArray['page']->getTheme());
		$secondArray['idpage'] = $secondArray['page']->getId();
		$secondArray['articles'] = null;
		
		$secondArray['images'] = array();
		if($secondArray['idpage']==1 ||$secondArray['idpage']==2 ||$secondArray['idpage']==5){
			$idArticle = 0;
			
			if ($secondArray['idpage']==2) $idArticle = 1;
			else $idArticle = 2;
			
			$secondArray['articles'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> findByType($idArticle);
		
			for($i=0;$i<count($secondArray['articles']);$i++){
				$secondArray['images'][$i] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['articles'][$i]->getMedia()); 
				$secondArray['articles'][$i]->setContent( substr ( $secondArray['articles'][$i]->getContent() , 0, 300 ) .'...');
															 }
		
		}

		
		
		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default2:page.html.twig', $array);
	}
	public function articleAction($id) {
		$firstArray = UtilitiesAPI::getDefaultContent('noticias', $this);
		$secondArray = array();
		$secondArray['article'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> find($id);
		$secondArray['resource'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['article']->getBackground());
		$secondArray['media'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['article']->getMedia());
		$secondArray['theme'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> find($secondArray['article']->getTheme());
		$secondArray['idpage'] = null;
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
		
	
		$secondArray['theme'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> find($secondArray['page']->getTheme());
		$secondArray['idpage'] = $secondArray['page']->getId();

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
		$secondArray['theme'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> find($secondArray['page']->getTheme());
		$secondArray['idpage'] = $secondArray['page']->getId();
		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default:talleres.html.twig', $array);
	}
	

	
	public function reservationAction($id, Request $request) {
		//$prueba = UtilitiesAPI::sendMail('Elecciones', array('name' => 'Juan','phone' =>'04249271991', 'email'=>'jonathan.araul@gmail.com' ),$this);
		
		$firstArray = UtilitiesAPI::getDefaultContent('reservation', $this);
		$secondArray = array();
		$secondArray['article'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> find($id);
		
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$data = new CmsReservation();
		$form = $this -> createFormBuilder($data) 
		-> add('name', 'text', array('required' => false))
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

			$secondArray['message'] = 'Estimado(a) '.ucwords($data -> getName()).' su reservación ha sido guardada exitosamente...';
			
														}
		
		$secondArray['form'] = $form -> createView();
		$secondArray['url'] =  $this -> generateUrl('proyecto_front_reservation', array('id' => $id));
		$secondArray['theme'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> find(6)->getColor();
		$secondArray['background'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find(50)->getWebPath();
		$secondArray['idpage'] = null;

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default:reservation.html.twig', $array);
	}

}
