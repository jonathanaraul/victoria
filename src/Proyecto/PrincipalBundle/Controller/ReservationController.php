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
use Proyecto\PrincipalBundle\Entity\CmsReservation;


class ReservationController extends Controller {

	public function listAction(Request $request) {

		$type = 'reservation';
		$config = UtilitiesAPI::getConfig($type,$this);
		$url = $this -> generateUrl('proyecto_principal_reservation_list');
		$firstArray = UtilitiesAPI::getDefaultContent('RESERVATION', $config['list'], $this);
		$firstArray['type'] = $config['type'];


		///////////////////////////////////////////////////////////////////////////////////////////////////
		$em = $this -> getDoctrine() -> getEntityManager();
		$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsReservation n";
		$query = $em -> createQuery($dql);
		

		$paginator = $this -> get('knp_paginator');
		$pagination = $paginator -> paginate($query, $this -> getRequest() -> query -> get('page', 1), 15);

		$objects = $pagination -> getItems();
		$auxiliar = array();

		for ($i = 0; $i < count($objects); $i++) {
			$auxiliar[$i]['id'] = $objects[$i] -> getId();
			$auxiliar[$i]['name'] =  ucfirst($objects[$i] -> getName()) . ' '. ucfirst($objects[$i] -> getLastName());
			$auxiliar[$i]['phone'] = $objects[$i] -> getPhone();
			$auxiliar[$i]['email'] = $objects[$i] -> getEmail();
			$auxiliar[$i]['date'] = $objects[$i] -> getDate()->format('d/m/Y H:m');
			$auxiliar[$i]['checked'] = $objects[$i] -> getChecked();
		}
		$objects = $auxiliar;

		$secondArray = array('pagination' => $pagination, 'objects' => $objects,  'url' => $url);
		$array = array_merge($firstArray, $secondArray);

		return $this -> render('ProyectoPrincipalBundle:Reservation:List.html.twig', $array);
	}
	
	public function editAction($id, Request $request) {

		$type = 'setting';
		$config = UtilitiesAPI::getConfig($type,$this);
		$firstArray = array();
		$firstArray = UtilitiesAPI::getDefaultContent('SETTING',$config['edit'], $this);

		$secondArray = array('accion' => 'editar');
		$secondArray['url'] = $this -> generateUrl('proyecto_principal_setting_edit', array('id' => $id));
		$secondArray['id'] = $id;
		$secondArray['lang'] = 0;
		$array = array_merge($firstArray, $secondArray);
		$array = array_merge($array, $config);

		return SettingController::normal($array, $request, $this);
	}

	public static function normal($array, Request $request, $class) {


		if ($array['accion'] == 'nuevo')
			$data = new CmsSetting();
		else
			$data = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsSetting') -> find($array['id']);


		$filtros = array();
		
		$form = $class -> createFormBuilder($data) 
		-> add('value', 'hidden', array('data' => '', 'required' => true )) 
		-> getForm();

		if ($class -> getRequest() -> isMethod('POST')) {

			$contenido = $request -> request -> all();
			$contenido = $contenido['page']['content'];
			
			$form -> bind($class -> getRequest());
			$em = $class -> getDoctrine() -> getManager();
			
			
			//CASO ESPECIAL MIRROR Y LANG
			$data -> setValue($contenido);
			$data -> setDateUpdated(new \DateTime());
			$data -> setIp($class -> container -> get('request') -> getClientIp());
			$data -> setUser($array['user'] -> getId());
			
			
			$em -> persist($data);

			$em -> flush();

			return $class -> redirect($class -> generateUrl('proyecto_principal_setting_list'));
				
			//}
			//if ($form -> isValid()) {}
		}

		$array['form'] = $form -> createView();
		
		$array['contenido'] = $array['form'] -> getVars();
		$array['contenido'] = $array['contenido']['value'] -> getValue();
		$array['auxname'] = $data -> getName();


		return $class -> render('ProyectoPrincipalBundle:Setting:New-Edit.html.twig', $array);
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
