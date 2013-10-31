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
use Proyecto\PrincipalBundle\Entity\CmsPage;
use Proyecto\PrincipalBundle\Entity\CmsPageTranslate;
use Proyecto\PrincipalBundle\Entity\CmsBackground;
use Proyecto\PrincipalBundle\Entity\CmsTheme;
use Proyecto\PrincipalBundle\Entity\CmsMedia;

class ArticleController extends Controller {

	public function listAction(Request $request) {
		$url = $this -> generateUrl('proyecto_principal_page_list');
		$firstArray = UtilitiesAPI::getDefaultContent('PAGINAS', 'Mostrar Información', 'Información', $this);

		$objects = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> findAll();
		$themes = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> findAll();
		$filtros['theme'] = array();
		$filtros['parentPage'] = array();
		$filtros['published'] = array(1 => 'Si', 0 => 'No');

		for ($i = 0; $i < count($themes); $i++) {
			$filtros['theme'][$themes[$i] -> getThemeId()] = $themes[$i] -> getName();
		}
		for ($i = 0; $i < count($objects); $i++) {
			$filtros['parentPage'][$objects[$i] -> getPageId()] = $objects[$i] -> getName();
		}

		///////////////////////////////////////////////////////////////////////////////////////////////////
		$data = new CmsPage();
		$form = $this -> createFormBuilder($data) -> add('name', 'text', array('required' => false)) -> add('parentPageId', 'choice', array('choices' => $filtros['parentPage'], 'required' => false, )) -> add('themeId', 'choice', array('choices' => $filtros['theme'], 'required' => false, )) -> add('published', 'choice', array('choices' => $filtros['published'], 'required' => false, )) -> getForm();

		$em = $this -> getDoctrine() -> getEntityManager();

		if ($this -> getRequest() -> isMethod('POST')) {
			$form -> bind($this -> getRequest());

			if ($form -> isValid()) {

				$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsPage n ";
				$where = false;

				if (!(trim($data -> getParentPageId()) == false)) {

					if ($where == false) {
						$dql .= 'WHERE ';
						$where = true;
					}
					$dql .= ' n.parentPageId = :parentPageId ';

				}
				if (!(trim($data -> getThemeId()) == false)) {

					if ($where == false) {
						$dql .= 'WHERE ';
						$where = true;
					} else {
						$dql .= 'AND ';
					}
					$dql .= ' n.themeId = :themeId ';

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

				$query = $em -> createQuery($dql);

				if (!(trim($data -> getParentPageId()) == false)) {
					$query -> setParameter('parentPageId', $data -> getParentPageId());
				}
				if (!(trim($data -> getThemeId()) == false)) {
					$query -> setParameter('themeId', $data -> getThemeId());
				}
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
			$dql = "SELECT n FROM ProyectoPrincipalBundle:CmsPage n";
			$query = $em -> createQuery($dql);
		}

		$paginator = $this -> get('knp_paginator');
		$pagination = $paginator -> paginate($query, $this -> getRequest() -> query -> get('page', 1), 15);

		$objects = $pagination -> getItems();
		$auxiliar = array();

		for ($i = 0; $i < count($objects); $i++) {
			$auxiliar[$i]['pageId'] = $objects[$i] -> getPageId();
			$auxiliar[$i]['parentPageId'] = ($objects[$i] -> getParentPageId() == 0) ? '-' : '' . $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find($objects[$i] -> getParentPageId()) -> getName();
			$auxiliar[$i]['name'] = $objects[$i] -> getName();
			$auxiliar[$i]['published'] = $objects[$i] -> getPublished();
			$auxiliar[$i]['backgroundId'] = ($objects[$i] -> getBackgroundId() == 0) ? '-' : '' . $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsBackground') -> find($objects[$i] -> getBackgroundId()) -> getFileName();
			$auxiliar[$i]['themeId'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> find($objects[$i] -> getThemeId()) -> getColor();
			$auxiliar[$i]['mediaId'] = ($objects[$i] -> getMediaId() == 0) ? '0' : '' . $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsMedia') -> find($objects[$i] -> getMediaId()) -> getFileNameMini();

		}
		$objects = $auxiliar;

		$secondArray = array('pagination' => $pagination, 'filtros' => $filtros, 'objects' => $objects, 'form' => $form -> createView(), 'url' => $url);

		$array = array_merge($firstArray, $secondArray);
		return $this -> render('ProyectoPrincipalBundle:Page:List.html.twig', $array);
	}

	public function rankAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('PAGINAS', 'Mostrar Información', 'Información', $this);

		$em = $this -> getDoctrine() -> getManager();
		$query = $em -> createQuery('SELECT p FROM ProyectoPrincipalBundle:CmsPage p ORDER BY p.rank ASC');
		$objects = $query -> getResult();

		$secondArray = array('objects' => $objects);
		$array = array_merge($firstArray, $secondArray);

		return $this -> render('ProyectoPrincipalBundle:Page:Rank.html.twig', $array);
	}

	public function rankPostAction() {

		$peticion = $this -> getRequest();
		$doctrine = $this -> getDoctrine();
		$post = $peticion -> request;
		//INICIALIZAR VARIABLES
		$order = $post -> get("order");
		$em = $this -> getDoctrine() -> getManager();
		for ($i = 0; $i < count($order); $i++) {

			$id = intval($order[$i]);
			$object = $em -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find($id);
			$object -> setRank($i);
			$em -> flush();

		}

		$estado = true;
		$respuesta = new response(json_encode(array('estado' => $estado)));
		$respuesta -> headers -> set('content_type', 'aplication/json');
		return $respuesta;
	}

	public function deleteAction() {

		$peticion = $this -> getRequest();
		$doctrine = $this -> getDoctrine();
		$post = $peticion -> request;
		//INICIALIZAR VARIABLES

		$id = $post -> get("id");
		$em = $this -> getDoctrine() -> getManager();
		$object = $em -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find($id);
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
		$object = $em -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find($id);
		$object -> setPublished($tarea);
		$em -> flush();

		$estado = true;
		$respuesta = new response(json_encode(array('estado' => $estado)));
		$respuesta -> headers -> set('content_type', 'aplication/json');
		return $respuesta;
	}

	public function newCreateAction(Request $request) {

		$firstArray = UtilitiesAPI::getDefaultContent('ARTICULOS', 'Nueva Pagina', 'Nuevo', $this);
		$secondArray = array('accion' => 'nuevo');
		$secondArray['url'] = $this -> generateUrl('proyecto_principal_article_new_create');
		$secondArray['type'] = 0;
		$secondArray['titulo'] = 'Nueva Noticia';
		
		$array = array_merge($firstArray, $secondArray);
		return ArticleController::procesarNormal($array, $request, $this);
	}

	public function editAction($id, Request $request) {

		$firstArray = UtilitiesAPI::getDefaultContent('PAGINAS', 'Editar Información', 'Editar', $this);
		$secondArray = array('accion' => 'edicion');
		$secondArray['url'] = $this -> generateUrl('proyecto_principal_page_edit', array('id' => $id));
		$secondArray['id'] = $id;

		$array = array_merge($firstArray, $secondArray);
		return PageController::procesarNormal($array, $request, $this);
	}

	public static function procesarNormal($array, Request $request, $class) {

		if ($array['accion'] == 'nuevo')
			$data = new CmsArticle();
		else
			$data = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find($array['id']);

		$objects = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> findAll();
		$themes = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> findAll();
		$media = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsMedia') -> findAll();
		$background = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsBackground') -> findAll();
		$category = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsCategory') -> findByType($array['type']);

		$filtros = array();
		$filtros['published'] = array(1 => 'Si', 0 => 'No');
		$filtros['theme'] = UtilitiesAPI::getFilterTheme($themes);
		$filtros['parentPage'] = UtilitiesAPI::getFilterParentPage($objects);
		$filtros['media'] = UtilitiesAPI::getFilterMedia($media);
		$filtros['background'] = UtilitiesAPI::getFilterBackground($background);
		$filtros['category'] = UtilitiesAPI::getFilterCategory($category);
		
		$form = $class -> createFormBuilder($data) 
		-> add('name', 'text', array('required' => true)) 
		-> add('category', 'choice', array('choices' => $filtros['category'], 'required' => true, )) 
		-> add('description', 'text', array('required' => true)) 
		-> add('keywords', 'text', array('required' => true)) 
		//-> add('content', 'hidden', array('data' => '', )) 
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
				
				$data -> setSuspended(0);
				$data -> setDateCreated(new \DateTime());
				$data -> setType($array['type']);
				
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

			return $class -> redirect($class -> generateUrl('proyecto_principal_page_list'));
			//if ($form -> isValid()) {}
		}

		$array['form'] = $form -> createView();
		$array['themes'] = $themes;
		$array['media'] = $media;

		$array['contenido'] = $array['form'] -> getVars();
		$array['contenido'] = $array['contenido']['value'] -> getContent();

		return $class -> render('ProyectoPrincipalBundle:Article:New-Edit.html.twig', $array);
	}

	public function translateAction($id, Request $request) {

		$firstArray = UtilitiesAPI::getDefaultContent('PAGINAS', 'Editar Información', 'Editar', $this);

		$data = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPageTranslate') -> findOneByPageId($id);
		$page = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> find($id);

		if (!$page) {
			throw $this -> createNotFoundException('La pagina que intenta traducir no existe ');
		}
		$secondArray = array();
		$secondArray['page'] = $page;


		if (!$data) {
			$secondArray['accion'] = 'nuevo';
			$secondArray['data'] = new CmsPageTranslate();
		} else {
			$secondArray['accion'] = 'edicion';
			$secondArray['data'] = $data;
		}

		$secondArray['url'] = $this -> generateUrl('proyecto_principal_page_translate', array('id' => $id));
		//$secondArray['id'] = $id;

		$array = array_merge($firstArray, $secondArray);
		return PageController::procesarTraduccion($array, $request, $this);
	}

	public static function procesarTraduccion($array, Request $request, $class) {

		$data = $array['data'];

		$objects = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsPage') -> findAll();
		$themes = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsTheme') -> findAll();
		$media = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsMedia') -> findAll();
		$background = $class -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:CmsBackground') -> findAll();

		$filtros = array();
		$filtros['langId'] = array(1 => 'english');


		$form = $class -> createFormBuilder($data) 
		-> add('name', 'text', array('required' => true)) 
		-> add('title', 'text', array('required' => true)) 
		-> add('descriptionMeta', 'text', array('required' => true)) 
		-> add('keywords', 'text', array('required' => true)) 
		-> add('content', 'hidden', array('data' => '', )) 
		-> add('upperText', 'text', array('required' => true)) 
		-> add('lowerText', 'text', array('required' => true)) 
		-> add('file', 'file', array('required' => false)) 
		-> add('langId', 'choice', array('choices' => $filtros['langId'], 'required' => true, )) 
		-> getForm();

		if ($class -> getRequest() -> isMethod('POST')) {

			$contenido = $request -> request -> all();
			$contenido = $contenido['page']['content'];

			$form -> bind($class -> getRequest());
			$em = $class -> getDoctrine() -> getManager();

			if ($array['accion'] == 'nuevo') {
				$data -> setPageId($array['page']->getPageId());
				$data -> setMediaId($array['page']->getMediaId());
				$data -> setBackgroundId($array['page']->getBackgroundId());
				$data -> setThemeId($array['page']->getThemeId());
				$data -> setPublished($array['page']->getPublished());
				$data -> setSuspended($array['page']->getSuspended());
				$data -> setRank($array['page']->getRank());
				$data -> setSuspended(0);
				$data -> setSpacer(0);
				$data -> setDescription('');
				$data -> setDateCreated(new \DateTime());
			} else {
				$data -> setDateUpdated(new \DateTime());
			}
			$data -> setContent($contenido);
			$data -> setIp($class -> container -> get('request') -> getClientIp());
			$data -> setUserId($array['user'] -> getId());
			$data -> setFriendlyName($data -> getTitle());//Modificar

			if ($array['accion'] == 'nuevo')
				$em -> persist($data);

			$em -> flush();
			return $class -> redirect($class -> generateUrl('proyecto_principal_page_list'));
			//if ($form -> isValid()) {}
		}
		$array['form'] = $form -> createView();

		$array['contenido'] = $array['form'] -> getVars();
		$array['contenido'] = $array['contenido']['value'] -> getContent();

		return $class -> render('ProyectoPrincipalBundle:Page:Translate.html.twig', $array);
	}



}
