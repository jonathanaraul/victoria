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

class DefaultController extends Controller {
	public function inicioAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('inicio', $this);
		$secondArray = array();
		$secondArray['backgrounds'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> findByHome(1);

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default2:inicio.html.twig', $array);
	}
	public function biografiaAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('biografia', $this);
		$secondArray = array();
		$secondArray['page'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find(3);
		$secondArray['resource'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getBackground());
		$secondArray['media'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getMedia());

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default2:biografia.html.twig', $array);
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
		return $this -> render('ProyectoFrontBundle:Default2:noticias.html.twig', $array);
	}
	public function articleAction($id) {
		$firstArray = UtilitiesAPI::getDefaultContent('noticias', $this);
		$secondArray = array();
		$secondArray['article'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> find($id);
		
		$secondArray['resource'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['article']->getBackground());
		$secondArray['media'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['article']->getMedia());
		
		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default2:article.html.twig', $array);
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
		return $this -> render('ProyectoFrontBundle:Default2:programacion.html.twig', $array);
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
		return $this -> render('ProyectoFrontBundle:Default2:talleres.html.twig', $array);
	}
	
	public function gastrobarAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('gastrobarla', $this);
		$secondArray = array();
		$secondArray['page'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find(33);
		
		$secondArray['background'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getBackground());
		$secondArray['media'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getMedia());
		$secondArray['theme'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> find($secondArray['page']->getTheme());

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default2:gastrobar.html.twig', $array);
	}
	public function contactoAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('contacto', $this);
		$secondArray = array();
		$secondArray['page'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find(14);
		$secondArray['background'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($secondArray['page']->getBackground());
		$secondArray['theme'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> find($secondArray['page']->getTheme());

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoFrontBundle:Default2:contacto.html.twig', $array);
	}

}
