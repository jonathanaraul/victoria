<?php

namespace Proyecto\PrincipalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Util\StringUtils;
use Proyecto\PrincipalBundle\Entity\Usuario;
use Proyecto\PrincipalBundle\Entity\CmsLink;


class LinkController extends Controller {

	public function listAction(Request $request) {
		
		$type = 'links';
		$config = UtilitiesAPI::getConfig($type,$this);
		$url = $this -> generateUrl('proyecto_principal_link_list');
		$firstArray = UtilitiesAPI::getDefaultContent('LINKS', $config['list'], $this);
		$firstArray['type'] = $config['type'];

		$filtros['published'] = array(1 => 'Si', 0 => 'No');
	

		///////////////////////////////////////////////////////////////////////////////////////////////////
		$data = new CmsLink();
		$form = $this -> createFormBuilder($data) 
		-> add('name', 'text', array('required' => false))
		-> add('published', 'choice', array('choices' => $filtros['published'], 'required' => false, )) 
		-> getForm();

		$em = $this -> getDoctrine() -> getEntityManager();

		if ($this -> getRequest() -> isMethod('POST')) {
			$form -> bind($this -> getRequest());

			if ($form -> isValid()) {

				$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsLink n ";
				$where = false;

				if (!(trim($data -> getName()) == false)) {

					if ($where == false) {
						$dql .= 'WHERE ';
						$where = true;
					} else {
						$dql .= 'AND ';
					}

					$dql .= " n.name like :name ";

				}
				if (!(trim($data -> getPublished()) == false)) {

					if ($where == false) {
						$dql .= 'WHERE ';
						$where = true;
					} else {
						$dql .= 'AND ';
					}
					$dql .= ' n.published = :published ';
				}

				$query = $em -> createQuery($dql);

				if (!(trim($data -> getName()) == false)) {
					$query -> setParameter('name', '%' . $data -> getName() . '%');
				}
				if (!(trim($data -> getPublished()) == false)) {
					$query -> setParameter('published', $data -> getPublished());
				}

			}
		}
		//////////////////////////////////////////////////////////////////////////////////////////////////
		else {
			$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsLink n";
			$query = $em -> createQuery($dql);
		}

		$paginator = $this -> get('knp_paginator');
		$pagination = $paginator -> paginate($query, $this -> getRequest() -> query -> get('page', 1), 15);

		$objects = $pagination -> getItems();
		$auxiliar = array();

		for ($i = 0; $i < count($objects); $i++) {
			$auxiliar[$i]['linkId'] = $objects[$i] -> getLinkId();
			$auxiliar[$i]['name'] = $objects[$i] -> getName();
			$auxiliar[$i]['www'] = $objects[$i] -> getWww();
			$auxiliar[$i]['newWindow'] = $objects[$i] -> getNewWindow();
			$auxiliar[$i]['published'] = $objects[$i] -> getPublished();
		}
		$objects = $auxiliar;

		$secondArray = array('pagination' => $pagination, 'filtros' => $filtros, 'objects' => $objects, 'form' => $form -> createView(), 'url' => $url);

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoPrincipalBundle:Link:List.html.twig', $array);
	}
	
	public function editAction($id, Request $request) {

		$type = 'links';
		$config = UtilitiesAPI::getConfig($type,$this);
		$firstArray = array();
		$firstArray = UtilitiesAPI::getDefaultContent('LINKS',$config['edit'], $this);

		$secondArray = array('accion' => 'editar');
		$secondArray['url'] = $this -> generateUrl('proyecto_principal_link_edit', array('id' => $id));
		$secondArray['id'] = $id;
		$array = array_merge($firstArray, $secondArray);
		$array = array_merge($array, $config);

		return LinkController::normal($array, $request, $this);
	}
	public function createAction(Request $request) {

		$type = 'links';		
		$config = UtilitiesAPI::getConfig($type,$this);
		$firstArray = array();
		$firstArray = UtilitiesAPI::getDefaultContent('LINKS',$config['create'], $this);

		$secondArray = array('accion' => 'nuevo');	
		$secondArray['url'] = $this -> generateUrl('proyecto_principal_link_create');
		$array = array_merge($firstArray, $secondArray);
		$array = array_merge($array, $config);

		return LinkController::normal($array, $request, $this);
	}
	public static function normal($array, Request $request, $class) {


		if ($array['accion'] == 'nuevo')
			$data = new CmsLink();
		else
			$data = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsLink') -> find($array['id']);


		$filtros = array();
		
		$form = $class -> createFormBuilder($data) 
		-> add('name', 'text', array('required' => true)) 
		-> add('www', 'text', array('required' => true)) 
		-> add('published', 'checkbox', array('label' => 'Publicado', 'required' => false, )) 
		-> add('newWindow', 'checkbox', array('label' => 'Publicado', 'required' => false, )) 
		-> getForm();

		if ($class -> getRequest() -> isMethod('POST')) {


			$form -> bind($class -> getRequest());
			$em = $class -> getDoctrine() -> getManager();
			
			//if ($form -> isValid()) {
			if ($array['accion'] == 'nuevo') {
				
				$data -> setSuspended(0);
				$data -> setDateCreated(new \DateTime());
				$data -> setRank(0);
				
			} else {
				$data -> setDateUpdated(new \DateTime());
			}
			
			$data -> setIp($class -> container -> get('request') -> getClientIp());
			$data -> setUserId($array['user'] -> getId());
			
			if ($array['accion'] == 'nuevo')
				$em -> persist($data);

			$em -> flush();

			return $class -> redirect($class -> generateUrl('proyecto_principal_link_list'));
				
			//}
			//if ($form -> isValid()) {}
		}

		$array['form'] = $form -> createView();

		return $class -> render('ProyectoPrincipalBundle:Link:New-Edit.html.twig', $array);
	}


	public function translateAction($type, $id, Request $request) {
		
		$config = UtilitiesAPI::getConfig($type,$this);
		$firstArray = array();
		$firstArray = UtilitiesAPI::getDefaultContent('ARTICULOS',$config['translate'], $this);

		$secondArray = array('accion' => 'editar');
		$secondArray['url'] = $this -> generateUrl('proyecto_principal_article_translate', array('type'=>$type,'id' => $id));
		$secondArray['id'] = $id;


		$data = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsArticleTranslate') -> findOneByArticle($id);
		$object = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> find($id);

		if (!$object) {
			throw $this -> createNotFoundException('El articulo que intenta traducir no existe ');
		}
		//$secondArray = array();
		$secondArray['object'] = $object;


		if (!$data) {
			$secondArray['accion'] = 'nuevo';
			$secondArray['data'] = new CmsArticleTranslate();
		} else {
			$secondArray['accion'] = 'editar';
			$secondArray['data'] = $data;
		}

		//$secondArray['id'] = $id;

		$array = array_merge($firstArray, $secondArray);
		$array = array_merge($array, $config);
		
		return ArticleController::traduccion($array, $request, $this);
	}

	public function deleteAction() {

		$peticion = $this -> getRequest();
		$doctrine = $this -> getDoctrine();
		$post = $peticion -> request;
		//INICIALIZAR VARIABLES

		$id = $post -> get("id");
		$em = $this -> getDoctrine() -> getManager();
		
		/*
		//Remover traduccion
		$object = $em -> getRepository('ProyectoPrincipalBundle:CmsArticleTranslate') -> findOneByArticle($id);
		if ($object) {
		$em -> remove($object);
		$em -> flush();
		}
		 * 
		 */
		//Remover original
		$object = $em -> getRepository('ProyectoPrincipalBundle:CmsLink') -> find($id);
		$em -> remove($object);
		$em -> flush();
		

		$estado = true;
		$respuesta = new response(json_encode(array('estado' => $estado)));
		$respuesta -> headers -> set('content_type', 'aplication/json');
		return $respuesta;
	}
	
	public function statusAction() {

		$peticion = $this -> getRequest();
		$doctrine = $this -> getDoctrine();
		$post = $peticion -> request;
		//INICIALIZAR VARIABLES

		$id = $post -> get("id");
		$tarea = intval($post -> get("tarea"));

		$em = $this -> getDoctrine() -> getManager();
		$object = $em -> getRepository('ProyectoPrincipalBundle:CmsLink') -> find($id);
		$object -> setPublished($tarea);
		$em -> flush();
		
		$estado = true;
		$respuesta = new response(json_encode(array('estado' => $estado)));
		$respuesta -> headers -> set('content_type', 'aplication/json');
		return $respuesta;
	}
	public function targetAction() {

		$peticion = $this -> getRequest();
		$doctrine = $this -> getDoctrine();
		$post = $peticion -> request;
		//INICIALIZAR VARIABLES

		$id = $post -> get("id");
		$tarea = intval($post -> get("tarea"));

		$em = $this -> getDoctrine() -> getManager();
		$object = $em -> getRepository('ProyectoPrincipalBundle:CmsLink') -> find($id);
		$object -> setNewWindow($tarea);
		$em -> flush();
		
		$estado = true;
		$respuesta = new response(json_encode(array('estado' => $estado)));
		$respuesta -> headers -> set('content_type', 'aplication/json');
		return $respuesta;
	}

}
