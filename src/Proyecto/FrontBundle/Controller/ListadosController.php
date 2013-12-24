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


class ListadosController extends Controller {

	const tamanio = 3;
	public function paginacionAction(){
		$peticion = $this -> getRequest();
		$doctrine = $this -> getDoctrine();
		$post = $peticion -> request;

		$em = $this -> getDoctrine() -> getEntityManager();	
		$type = UtilitiesAPI::TIPO_NOTICIAS;
		$numeroPagina = $post -> get("valor");
			
		$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsArticle n WHERE n.type = :type1 order by n.dateCreated DESC";
			
		$query = $em -> createQuery($dql);
		$query -> setParameter('type1', $type);
		
		$paginator = $this -> get('knp_paginator');
		$pagination = $paginator -> paginate($query, $this -> getRequest() -> query -> get('page', $numeroPagina), ListadosController::tamanio
		);

		$array['articles'] = $pagination -> getItems();
		$array['izquierda'] =  $numeroPagina - 1;
		$array['derecha'] =  $numeroPagina + 1;
		
		$dataPaginacion = $pagination->getPaginationData();
		
		if($array['derecha'] > $dataPaginacion['pageRange']) $array['derecha'] =0;
		
		for($i=0;$i<count($array['articles']);$i++){
			$array['images'][$i] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($array['articles'][$i]->getMedia()); 
			$array['articles'][$i]->setContent( substr ( $array['articles'][$i]->getContent() , 0, 300 ) .'...');
												   }

		$html = $this -> renderView('ProyectoFrontBundle:Default:noticias.html.twig', $array);
		
		$respuesta = new response(json_encode(array('variable'=>$html)));
		$respuesta -> headers -> set('content_type', 'aplication/json');
		return $respuesta;
	}
	public function noticiasAction($id) {
			
		$em = $this -> getDoctrine() -> getEntityManager();	
		$type = UtilitiesAPI::TIPO_NOTICIAS;
		$numeroPagina =1;
			
		$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsArticle n WHERE n.type = :type1 order by n.dateCreated DESC";
			
		$query = $em -> createQuery($dql);
		$query -> setParameter('type1', $type);
		
		$paginator = $this -> get('knp_paginator');
		$pagination = $paginator -> paginate($query, $this -> getRequest() -> query -> get('page', $numeroPagina), ListadosController::tamanio
		);

		$array['articles'] = $pagination -> getItems();
		$array['izquierda'] =  $numeroPagina - 1;
		$array['derecha'] =  $numeroPagina + 1;
		
		$dataPaginacion = $pagination->getPaginationData();
		
		if($array['derecha'] > $dataPaginacion['pageRange']) $array['derecha'] =0;
		
		for($i=0;$i<count($array['articles']);$i++){
			$array['images'][$i] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($array['articles'][$i]->getMedia()); 
			$array['articles'][$i]->setContent( substr ( $array['articles'][$i]->getContent() , 0, 300 ) .'...');
												   }

		return $this -> render('ProyectoFrontBundle:Default:noticias.html.twig', $array);
	}
	public function carteleraAction($id) {
			
		$em = $this -> getDoctrine() -> getEntityManager();	
		$type = UtilitiesAPI::TIPO_CARTELERA;
			
		$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsArticle n WHERE n.type = :type1 order by n.dateCreated DESC";
			
		$query = $em -> createQuery($dql);
		$query -> setParameter('type1', $type);
			
		$array['articles'] = $query -> getResult();

		for($i=0;$i<count($array['articles']);$i++){
			$array['images'][$i] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($array['articles'][$i]->getMedia()); 
			$array['articles'][$i]->setContent( substr ( $array['articles'][$i]->getContent() , 0, 300 ) .'...');
												   }

		return $this -> render('ProyectoFrontBundle:Default:generico.html.twig', $array);
	}
	public function talleresAction($id) {
			
		$em = $this -> getDoctrine() -> getEntityManager();	
		$type = UtilitiesAPI::TIPO_TALLERES;
			
		$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsArticle n WHERE n.type = :type1 order by n.dateCreated DESC";
			
		$query = $em -> createQuery($dql);
		$query -> setParameter('type1', $type);
			
		$array['articles'] = $query -> getResult();

		for($i=0;$i<count($array['articles']);$i++){
			$array['images'][$i] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($array['articles'][$i]->getMedia()); 
			$array['articles'][$i]->setContent( substr ( $array['articles'][$i]->getContent() , 0, 300 ) .'...');
												   }

		return $this -> render('ProyectoFrontBundle:Default:generico.html.twig', $array);
	}
	public function calendarioAction($id) {
			
		$em = $this -> getDoctrine() -> getEntityManager();	
		$type =  UtilitiesAPI::TIPO_CARTELERA;
		$type2 = UtilitiesAPI::TIPO_TALLERES;
			
		$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsArticle n WHERE n.type = :type1  or n.type = :type2 order by n.dateCreated DESC";
			
		$query = $em -> createQuery($dql);
		$query -> setParameter('type1', $type);
		$query -> setParameter('type2', $type2);
			
		$array['articles'] = $query -> getResult();

		for($i=0;$i<count($array['articles']);$i++){
			$array['images'][$i] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($array['articles'][$i]->getMedia()); 
			$array['articles'][$i]->setContent( substr ( $array['articles'][$i]->getContent() , 0, 300 ) .'...');
												   }

		return $this -> render('ProyectoFrontBundle:Default:generico.html.twig', $array);
	}
}
