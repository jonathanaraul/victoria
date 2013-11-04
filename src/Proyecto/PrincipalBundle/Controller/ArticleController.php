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
use Proyecto\PrincipalBundle\Entity\CmsArticle;
use Proyecto\PrincipalBundle\Entity\CmsArticleTranslate;
use Proyecto\PrincipalBundle\Entity\CmsCategory;
use Proyecto\PrincipalBundle\Entity\CmsPage;
use Proyecto\PrincipalBundle\Entity\CmsPageTranslate;
use Proyecto\PrincipalBundle\Entity\CmsBackground;
use Proyecto\PrincipalBundle\Entity\CmsTheme;
use Proyecto\PrincipalBundle\Entity\CmsMedia;

class ArticleController extends Controller {

	public function listAction($type,Request $request) {
		

		$config = UtilitiesAPI::getConfig($type,$this);
		$url = $this -> generateUrl('proyecto_principal_article_list',array('type' => $type));
		$firstArray = UtilitiesAPI::getDefaultContent('ARTICULOS', $config['list'], $this);
		$firstArray['type'] = $config['type'];
		
		$category = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsCategory') -> findByType($config['idtype']);

		$filtros['published'] = array(1 => 'Si', 0 => 'No');
		$filtros['category'] = UtilitiesAPI::getFilterCategory($category);

		///////////////////////////////////////////////////////////////////////////////////////////////////
		$data = new CmsArticle();
		$form = $this -> createFormBuilder($data) 
		-> add('name', 'text', array('required' => false))
		-> add('published', 'choice', array('choices' => $filtros['published'], 'required' => false, )) 
		-> add('category', 'choice', array('choices' => $filtros['category'], 'required' => false, )) 
		-> getForm();

		$em = $this -> getDoctrine() -> getEntityManager();

		if ($this -> getRequest() -> isMethod('POST')) {
			$form -> bind($this -> getRequest());

			if ($form -> isValid()) {

				$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsArticle n ";
				$where = false;

				if (!(trim($data -> getCategory()) == false)) {

					if ($where == false) {
						$dql .= 'WHERE ';
						$where = true;
					}
					$dql .= ' n.category = :category ';

				}
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

				if ($where == false) {
						$dql .= 'WHERE ';
						$where = true;
				} else {
						$dql .= 'AND ';
				}
				$dql .= ' n.type = :type ';


				$query = $em -> createQuery($dql);

				if (!(trim($data -> getCategory()) == false)) {
					$query -> setParameter('category', $data -> getCategory());
				}
				if (!(trim($data -> getName()) == false)) {
					$query -> setParameter('name', '%' . $data -> getName() . '%');
				}
				if (!(trim($data -> getPublished()) == false)) {
					$query -> setParameter('published', $data -> getPublished());
				}
				$query -> setParameter('type', $config['idtype']);

			}
		}
		//////////////////////////////////////////////////////////////////////////////////////////////////
		else {
			$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsArticle n WHERE n.type = :type";
			$query = $em -> createQuery($dql);
			$query -> setParameter('type', $config['idtype']);
		}

		$paginator = $this -> get('knp_paginator');
		$pagination = $paginator -> paginate($query, $this -> getRequest() -> query -> get('page', 1), 10);

		$objects = $pagination -> getItems();
		$auxiliar = array();

		for ($i = 0; $i < count($objects); $i++) {
			$auxiliar[$i]['id'] = $objects[$i] -> getId();
			$auxiliar[$i]['category'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsCategory') -> find($objects[$i] -> getCategory()) -> getName();
			$auxiliar[$i]['name'] = $objects[$i] -> getName();
			$auxiliar[$i]['published'] = $objects[$i] -> getPublished();
			$auxiliar[$i]['dateCreated'] = $objects[$i] -> getDateCreated()->format('d/m/Y');
			$auxiliar[$i]['media'] = ($objects[$i] -> getMedia() == 0) ? '0' : '' . $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> find($objects[$i] -> getMedia()) -> getWebPath();

		}
		$objects = $auxiliar;

		$secondArray = array('pagination' => $pagination, 'filtros' => $filtros, 'objects' => $objects, 'form' => $form -> createView(), 'url' => $url);

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoPrincipalBundle:Article:List.html.twig', $array);
	}
	
	public function editAction($type,$id, Request $request) {

		$config = UtilitiesAPI::getConfig($type,$this);
		$firstArray = array();
		$firstArray = UtilitiesAPI::getDefaultContent('ARTICULOS',$config['edit'], $this);

		$secondArray = array('accion' => 'editar');
		$secondArray['url'] = $this -> generateUrl('proyecto_principal_article_edit', array('type'=>$type,'id' => $id));
		$secondArray['id'] = $id;
		$secondArray['lang'] = 0;
		$array = array_merge($firstArray, $secondArray);
		$array = array_merge($array, $config);

		return ArticleController::normal($array, $request, $this);
	}
	public function createAction($type,Request $request) {
		
		$config = UtilitiesAPI::getConfig($type,$this);
		$firstArray = array();
		$firstArray = UtilitiesAPI::getDefaultContent('ARTICULOS',$config['create'], $this);

		$secondArray = array('accion' => 'nuevo');	
		$secondArray['lang'] = 0;
		$secondArray['url'] = $this -> generateUrl('proyecto_principal_article_create',array('type' => $type));
		$array = array_merge($firstArray, $secondArray);
		$array = array_merge($array, $config);

		return ArticleController::normal($array, $request, $this);
	}
	public static function normal($array, Request $request, $class) {


		if ($array['accion'] == 'nuevo')
			$data = new CmsArticle();
		else
			$data = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> find($array['id']);

		$objects = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> findAll();
		$themes = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> findAll();
		$category = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsCategory') -> findByType($array['idtype']);
		$media = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> findByType(3);
		$background = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsResource') -> findByType(4);

		$filtros = array();
		$filtros['published'] = array(1 => 'Si', 0 => 'No');
		$filtros['theme'] = UtilitiesAPI::getFilter($themes);
		$filtros['parentPage'] = UtilitiesAPI::getFilter($objects);
		$filtros['media'] = UtilitiesAPI::getFilter($media);
		$filtros['background'] = UtilitiesAPI::getFilter($background);
		$filtros['category'] = UtilitiesAPI::getFilter($category);
		
		$form = $class -> createFormBuilder($data) 
		-> add('name', 'text', array('required' => true)) 
		-> add('category', 'choice', array('choices' => $filtros['category'], 'required' => true, )) 
		-> add('description', 'text', array('required' => true)) 
		-> add('keywords', 'text', array('required' => true)) 
		-> add('content', 'hidden', array('data' => '', )) 
		-> add('file', 'file', array('required' => false)) 
		-> add('theme', 'choice', array('choices' => $filtros['theme'], 'required' => true, )) 
		-> add('media', 'choice', array('choices' => $filtros['media'], 'required' => true, )) 
		-> add('background', 'choice', array('choices' => $filtros['background'], 'required' => true, )) 
		-> add('published', 'checkbox', array('label' => 'Publicado', 'required' => false, )) 
		-> getForm();

		if ($class -> getRequest() -> isMethod('POST')) {

			$contenido = $request -> request -> all();
			$contenido = $contenido['page']['content'];

			$form -> bind($class -> getRequest());
			$em = $class -> getDoctrine() -> getManager();

			if ($array['accion'] == 'nuevo') {
				$data -> setLang($array['lang']);
				$data -> setMirror(0);
				$data -> setSuspended(0);
				$data -> setDateCreated(new \DateTime());
				$data -> setType($array['idtype']);
				
			} else {
				$data -> setDateUpdated(new \DateTime());
			}
			$data -> setContent($contenido);
			$data -> setIp($class -> container -> get('request') -> getClientIp());
			$data -> setUser($array['user'] -> getId());
			$data -> setFriendlyName($data -> getName());

			if ($array['accion'] == 'nuevo')
				$em -> persist($data);

			$em -> flush();

			return $class -> redirect($class -> generateUrl('proyecto_principal_article_list',array('type' => $array['type'])));
			//if ($form -> isValid()) {}
		}

		$array['form'] = $form -> createView();
		$array['themes'] = $themes;
		$array['media'] = $media;

		$array['contenido'] = $array['form'] -> getVars();
		$array['contenido'] = $array['contenido']['value'] -> getContent();

		return $class -> render('ProyectoPrincipalBundle:Article:New-Edit.html.twig', $array);
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

	public static function traduccion($array, Request $request, $class) {
		

		$data = $array['data'];

		$objects = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> findAll();
		$themes = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> findAll();
		$media = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsMedia') -> findAll();
		$background = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsBackground') -> findAll();

		$filtros = array();
		$filtros['lang'] = array(1 => 'english');
		$filtros['published'] = array(1 => 'Si', 0 => 'No');

		$form = $class -> createFormBuilder($data) 
		-> add('name', 'text', array('required' => true)) 
		-> add('description', 'text', array('required' => true)) 
		-> add('keywords', 'text', array('required' => true)) 
		-> add('content', 'hidden', array('data' => '', )) 
		-> add('file', 'file', array('required' => false)) 
		-> add('lang', 'choice', array('choices' => $filtros['lang'], 'required' => true, )) 
		-> add('published', 'checkbox', array('label' => 'Publicado', 'required' => false, )) 
		-> getForm();

		if ($class -> getRequest() -> isMethod('POST')) {

			$contenido = $request -> request -> all();
			$contenido = $contenido['page']['content'];

			$form -> bind($class -> getRequest());
			$em = $class -> getDoctrine() -> getManager();

			if ($array['accion'] == 'nuevo') {
				$data -> setArticle($array['object']->getId());
				$data -> setDateCreated(new \DateTime());
			} else {
				$data -> setDateUpdated(new \DateTime());
			}
			$data -> setContent($contenido);
			$data -> setIp($class -> container -> get('request') -> getClientIp());
			$data -> setUser($array['user'] -> getId());
			$data -> setFriendlyName($data -> getName());//Modificar

			if ($array['accion'] == 'nuevo')
				$em -> persist($data);

			$em -> flush();
			return $class -> redirect($class -> generateUrl('proyecto_principal_article_list',array('type' => $array['type'])));
			//if ($form -> isValid()) {}
		}

		$array['form'] = $form -> createView();

		$array['contenido'] = $array['form'] -> getVars();
		$array['contenido'] = $array['contenido']['value'] -> getContent();

		return $class -> render('ProyectoPrincipalBundle:Article:Translate.html.twig', $array);
	}
	public function deleteAction() {

		$peticion = $this -> getRequest();
		$doctrine = $this -> getDoctrine();
		$post = $peticion -> request;
		//INICIALIZAR VARIABLES

		$id = $post -> get("id");
		$em = $this -> getDoctrine() -> getManager();
		
		//Remover traduccion
		$object = $em -> getRepository('ProyectoPrincipalBundle:CmsArticleTranslate') -> findOneByArticle($id);
		if ($object) {
		$em -> remove($object);
		$em -> flush();
		}
		//Remover original
		$object = $em -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> find($id);
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
		$object = $em -> getRepository('ProyectoPrincipalBundle:CmsArticle') -> find($id);
		$object -> setPublished($tarea);
		$em -> flush();
		
		//Codigo a borrar a futuro e incluir el elemento pusblished en las traducciones
		$object = $em -> getRepository('ProyectoPrincipalBundle:CmsArticleTranslate') -> findOneByArticle($id);
		if ($object) {
		$object -> setPublished($tarea);
		$em -> flush();
		}

		$estado = true;
		$respuesta = new response(json_encode(array('estado' => $estado)));
		$respuesta -> headers -> set('content_type', 'aplication/json');
		return $respuesta;
	}

}
