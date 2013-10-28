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
use Proyecto\PrincipalBundle\Entity\CmsPage;
use Proyecto\PrincipalBundle\Entity\CmsBackground;
use Proyecto\PrincipalBundle\Entity\CmsTheme;
use Proyecto\PrincipalBundle\Entity\CmsMedia;

class PageController extends Controller {

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
		$em = $this->getDoctrine()->getManager();
		for ($i=0; $i < count($order); $i++) {

			$id = intval($order[$i]);
    		$object = $em->getRepository('ProyectoPrincipalBundle:CmsPage') -> find($id);
			$object->setRank($i);
    		$em->flush();

			 
		}



		$estado = true;
		$respuesta = new response(json_encode(array('estado' => $estado)));
		$respuesta -> headers -> set('content_type', 'aplication/json');
		return $respuesta;
	}

	public function createAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('PAGINAS', 'Mostrar Información', 'Información', $this);
		$secondArray = array();

		$array = array_merge($firstArray, $secondArray);

		return $this -> render('ProyectoPrincipalBundle:Page:Create.html.twig', $array);
	}

	public function editAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('TU INFORMACIÓN', 'Mostrar Información', 'Editar', $this);
		$secondArray = array('accion' => 'edicion');
		$secondArray['url'] = $this -> generateUrl('proyecto_principal_user_edit');

		$array = array_merge($firstArray, $secondArray);
		return UserController::procesar($array, $this);

		//return $this -> render('ProyectoPrincipalBundle:User:Edit.html.twig', $array);
	}

	public function newAction() {
		$firstArray = UtilitiesAPI::getDefaultContent('TU INFORMACIÓN', 'Nuevo Información', 'Nuevo', $this);
		$secondArray = array('accion' => 'registro');
		$secondArray['url'] = $this -> generateUrl('proyecto_principal_user_new');

		$array = array_merge($firstArray, $secondArray);
		return UserController::procesar($array, $this);

		//return $this -> render('ProyectoPrincipalBundle:User:Edit.html.twig', $array);
	}

	public static function procesar($array, $class) {

		if ($array['accion'] == 'registro')
			$data = new Usuario();
		else
			$data = $array['user'];

		$form = $class -> createFormBuilder($data) -> add('username', 'text') -> add('password', 'repeated', array('first_name' => 'password', 'second_name' => 'confirm', 'type' => 'password', )) -> add('email', 'email') -> getForm();

		if ($class -> getRequest() -> isMethod('POST')) {
			$form -> bind($class -> getRequest());

			if ($form -> isValid()) {

				$em = $class -> getDoctrine() -> getManager();
				$factory = $class -> get('security.encoder_factory');
				$encoder = $factory -> getEncoder($data);
				$password = $encoder -> encodePassword($data -> getPassword(), $data -> getSalt());
				$data -> setPassword($password);
				$em -> persist($data);
				$em -> flush();

				return $class -> redirect($class -> generateUrl('proyecto_principal_homepage'));

			}
		}
		$array['form'] = $form -> createView();
		return $class -> render('ProyectoPrincipalBundle:User:New-Edit.html.twig', $array);
	}

	public function loginAction() {

		$error = NULL;
		$ultimo_nombreusuario = null;

		$peticion = $this -> getRequest();
		$sesion = $peticion -> getSession();
		// obtiene el error de inicio de sesión si lo hay
		if ($peticion -> attributes -> has(SecurityContext::AUTHENTICATION_ERROR))
			$error = $peticion -> attributes -> get(SecurityContext::AUTHENTICATION_ERROR);
		else
			$error = $sesion -> get(SecurityContext::AUTHENTICATION_ERROR);

		$firstArray = array();
		//UtilitiesAPI::getDefaultContent('Usuarios', 'Acceso', 'Acceso', 'Ingrese su nombre de usuario y su contraseña', $this);
		$secondArray = array('ultimo_nombreusuario' => $sesion -> get(SecurityContext::LAST_USERNAME), 'error' => $error);

		$array = array_merge($firstArray, $secondArray);
		//return $this -> render('ProyectoPrincipalBundle:Principal2:index.html.twig', $array);
		return $this -> render('ProyectoPrincipalBundle:User:Login.html.twig', $array);
	}

}
