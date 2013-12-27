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
	public function paginacionEspecialAction(){
		
		$fecha = UtilitiesAPI::fechaHoy($this);
		$array = array();
		$fecha = new \DateTime($fecha);
		
		
		
		$array['fechas'][0]['fecha']= $fecha->format('Y-m-d');
		$array['fechas'][0]['dia']= $fecha->format('d').' '.UtilitiesAPI::letraDia($fecha->format('w'),$this);
		$fecha->add(new \DateInterval('P1D'));
		$array['fechas'][1]['fecha']= $fecha->format('Y-m-d');
		$array['fechas'][1]['dia']= $fecha->format('d').' '.UtilitiesAPI::letraDia($fecha->format('w'),$this);
		$fecha->add(new \DateInterval('P1D'));
		$array['fechas'][2]['fecha']= $fecha->format('Y-m-d');
		$array['fechas'][2]['dia']= $fecha->format('d').' '.UtilitiesAPI::letraDia($fecha->format('w'),$this);
		$fecha->add(new \DateInterval('P1D'));
		$array['fechas'][3]['fecha']= $fecha->format('Y-m-d');
		$array['fechas'][3]['dia']= $fecha->format('d').' '.UtilitiesAPI::letraDia($fecha->format('w'),$this);
		$fecha->add(new \DateInterval('P1D'));
		$array['fechas'][4]['fecha']= $fecha->format('Y-m-d');
		$array['fechas'][4]['dia']= $fecha->format('d').' '.UtilitiesAPI::letraDia($fecha->format('w'),$this);
		$fecha->add(new \DateInterval('P1D'));
		$array['fechas'][5]['fecha']= $fecha->format('Y-m-d');
		$array['fechas'][5]['dia']= $fecha->format('d').' '.UtilitiesAPI::letraDia($fecha->format('w'),$this);
		
		return $this -> render('ProyectoFrontBundle:Default:paginacionespecial.html.twig', $array);
	
	}
	public function noticiasAction() {
			
		$peticion = $this -> getRequest();
		$doctrine = $this -> getDoctrine();
		$post = $peticion -> request;
		$numeroPagina = $post -> get("valor");
		
		if(!isset($numeroPagina)){
			$numeroPagina =1;
			return $this -> render('ProyectoFrontBundle:Default:noticias.html.twig',  ListadosController::contenidoNoticias($numeroPagina,$this));
		}
		else{
			$html = $this -> renderView('ProyectoFrontBundle:Default:noticias.html.twig', ListadosController::contenidoNoticias($numeroPagina,$this));
			$respuesta = new response(json_encode(array('variable'=>$html)));
			$respuesta -> headers -> set('content_type', 'aplication/json');
			return $respuesta;
		}
	}
	public function contenidoNoticias($numeroPagina,$class){
			
		$em = $class -> getDoctrine() -> getEntityManager();	
		$type = UtilitiesAPI::TIPO_NOTICIAS;
		
		$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsArticle n WHERE n.type = :type1 and n.published = :published and n.lang = :lang order by n.dateCreated DESC";
			
		$query = $em -> createQuery($dql);
		$query -> setParameter('type1', $type);
		$query -> setParameter('published', 1);
		$query -> setParameter('lang', UtilitiesAPI::getLocale($this));

		$paginator = $class -> get('knp_paginator');
		$pagination = $paginator -> paginate($query, $class -> getRequest() -> query -> get('page', $numeroPagina), ListadosController::tamanio
		);

		$array['articles'] = $pagination -> getItems();
		$array['izquierda'] =  $numeroPagina - 1;
		$array['derecha'] =  $numeroPagina + 1;
		
		$dataPaginacion = $pagination->getPaginationData();
		
		if($array['derecha'] > $dataPaginacion['pageRange']) $array['derecha'] =0;
		
		for($i=0;$i<count($array['articles']);$i++){
			$array['images'][$i] = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($array['articles'][$i]->getMedia()); 
			$array['articles'][$i]->setContent( substr ( $array['articles'][$i]->getContent() , 0, 300 ) .'...');
												   }
		return $array;
	}
	public function carteleraAction() {
					
		$peticion = $this -> getRequest();
		$doctrine = $this -> getDoctrine();
		$post = $peticion -> request;
		$fecha = $post -> get("valor");
		$type = UtilitiesAPI::TIPO_CARTELERA;
		
		if(!isset($fecha)){
			$fecha =  UtilitiesAPI::fechaInicioFin(UtilitiesAPI::fechaHoy($this),$this);
			return $this -> render('ProyectoFrontBundle:Default:cartelera.html.twig',  ListadosController::contenidoGenerico($fecha,$type,$this));
		}
		else{
			$fecha =  UtilitiesAPI::fechaInicioFin($fecha,$this);
			$html = $this -> renderView('ProyectoFrontBundle:Default:cartelera.html.twig', ListadosController::contenidoGenerico($fecha,$type,$this));
			$respuesta = new response(json_encode(array('variable'=>$html)));
			$respuesta -> headers -> set('content_type', 'aplication/json');
			return $respuesta;
		}

	}
	public function contenidoGenerico($fecha,$type,$class){	
		$em = $class -> getDoctrine() -> getEntityManager();	
		
		$dql = "SELECT n
		        FROM ProyectoPrincipalBundle:CmsDate n, 
		             ProyectoPrincipalBundle:CmsArticle a
		        WHERE n.date >= :fecha1 
		          and n.date <= :fecha2 
		          and n.article = a.id
		          and a.published = :published
		          and a.lang = :lang
		          and a.type = :type1
		        ORDER by n.date ASC";
		
		$query = $em -> createQuery($dql);
		
		$query -> setParameter('fecha1', $fecha['inicio']);
		$query -> setParameter('fecha2', $fecha['fin']);
		
		$query -> setParameter('type1', $type);
		$query -> setParameter('published', 1);
		$query -> setParameter('lang', UtilitiesAPI::getLocale($this));
		
		$array['articles'] = $query -> getResult();

		for($i=0;$i<count($array['articles']);$i++){
				$array['images'][$i] = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($array['articles'][$i]->getArticle()->getMedia()); 
				$array['articles'][$i]->getArticle()->setContent( substr ( $array['articles'][$i]->getArticle()->getContent() , 0, 300 ) .'...');
         }

		return $array;
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
