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
	public function carteleraOctubreAction() {
		$array = UtilitiesAPI::getDefaultContent('carteleraoctubre', $this);

		return $this -> render('ProyectoFrontBundle:Default2:carterleraoctubre.html.twig', $array);
	}
	public function cursoRegularAction() {
		$array = UtilitiesAPI::getDefaultContent('cursoregular', $this);

		return $this -> render('ProyectoFrontBundle:Default2:cursoregular.html.twig', $array);
	}
	public function tallerTeatroAction() {
		$array = UtilitiesAPI::getDefaultContent('tallerteatro', $this);

		return $this -> render('ProyectoFrontBundle:Default2:tallerteatro.html.twig', $array);
	}
	public function losMartesAction() {
		$array = UtilitiesAPI::getDefaultContent('losmartes', $this);

		return $this -> render('ProyectoFrontBundle:Default2:losmartes.html.twig', $array);
	}
	public function gastrobarAction() {
		$array = UtilitiesAPI::getDefaultContent('gastrobarla', $this);

		return $this -> render('ProyectoFrontBundle:Default2:gastrobarla.html.twig', $array);
	}
	public function presentanosAction() {
		$array = UtilitiesAPI::getDefaultContent('presentanostu', $this);

		return $this -> render('ProyectoFrontBundle:Default2:presentanos.html.twig', $array);
	}
	public function informacionReservasAction() {
		$array = UtilitiesAPI::getDefaultContent('informaciony', $this);

		return $this -> render('ProyectoFrontBundle:Default2:informacionreservas.html.twig', $array);
	}
	public function reservasContactoAction() {
		$array = UtilitiesAPI::getDefaultContent('reservasy', $this);

		return $this -> render('ProyectoFrontBundle:Default2:reservascontacto.html.twig', $array);
	}
/*
	public function organizacionAction() {
		$array = UtilitiesAPI::getDefaultContent('organizacion', $this);
		$array['titulo'] = 'La Organizaci&oacute;n';
		$array['objects'] = APIController::findDataByTitleCategory($array['menu'], $this);
		$array['ruta'] = 'proyecto_front_organizacion_articulo';

		return $this -> render('ProyectoFrontBundle:Default:articulos.html.twig', $array);
	}

	public function informacionAction() {
		$array = UtilitiesAPI::getDefaultContent('informacion', $this);
		$array['titulo'] = 'Informaci&oacute;n';
		$array['ruta'] = 'proyecto_front_informacion_articulo';
		$array['objects'] = APIController::findDataByTitleCategory($array['menu'], $this);

		return $this -> render('ProyectoFrontBundle:Default:articulos.html.twig', $array);
	}

	public function articuloAction($id) {
		$array = UtilitiesAPI::getDefaultContent('articulo', $this);
		$array['object'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:Data') -> find($id);

		if (!$array['object']) {
			throw $this -> createNotFoundException('Articulo no encontrado');
		}

		if ($array['object'] -> getCategoria() -> getTitulo() != 'informacion' && $array['object'] -> getCategoria() -> getTitulo() != 'organizacion') {
			throw $this -> createNotFoundException('Articulo no encontrado');
		}

		$array['menu'] = $array['object'] -> getCategoria() -> getTitulo();

		return $this -> render('ProyectoFrontBundle:Default:articulo.html.twig', $array);
	}

	public function descargasAction() {
		$array = UtilitiesAPI::getDefaultContent('descargas', $this);
		$array['objects'] = $this -> getDoctrine() -> getRepository('ProyectoPrincipalBundle:Categoria') -> findByTipo(1);

		$em = $this -> getDoctrine() -> getManager();
		$query = $em -> createQuery('SELECT d
    								 FROM ProyectoPrincipalBundle:Data d,
    	 								  ProyectoPrincipalBundle:Categoria c
   	 								 WHERE d.categoria = c.id AND
   	 								       c.tipo = :tipo
    								 ORDER BY d.titulo ASC') -> setParameter('tipo', '1');

		$array['downloads'] = $query -> getResult();

		return $this -> render('ProyectoFrontBundle:Default:descargas.html.twig', $array);
	}

	public function contactoAction() {
		$array = UtilitiesAPI::getDefaultContent('contacto', $this);

		$data = new Entrada();
		if($array['usuario']!=null){
			$data->setNombre($array['usuario']->getNombre() .' '.$array['usuario']->getApellido());
			$data->setEmail($array['usuario']->getEmail());
		}
		$tipo = '3';
		//3= Entrada de archivos

		$form = $this -> createFormBuilder($data) 
				-> add('nombre', 'text',array('disabled' => true) ) 
				-> add('email', 'text',array('disabled' => true) ) 
				-> add('mensaje', 'textarea') 
				-> add('categoria', 'entity', array('class' => 'ProyectoPrincipalBundle:Categoria', 'query_builder' => function(EntityRepository $er) use ($tipo) {
			return $er -> createQueryBuilder('sc') -> where('sc.tipo = :tipo') -> setParameter('tipo', $tipo);
		}, )) 
				-> add('file') -> getForm();

		if ($this -> getRequest() -> isMethod('POST')) {
			$form -> bind($this -> getRequest());

			if ($form -> isValid()) {

				//$array = array();
				$array['mensaje'] = 'Su mensaje fue enviado exitosamente...Su respuesta sera enviada a su correo electr&oacute;nico y a su vez podr&aacute; leerla a trav&eacute;s de su perfil en nuestro portal web cuando sea procesada...';
				$array['titulo'] = 'Mensaje Enviado';

				$em = $this -> getDoctrine() -> getManager();
				$data -> setLeido(0);
				$data -> setRespondido(0);
				$data -> setFecha(new \DateTime());
				$em -> persist($data);
				$em -> flush();
				return $this -> render('ProyectoFrontBundle:Helpers:mensaje.html.twig', $array);
			}
		}

		$array['form'] = $form -> createView();

		return $this -> render('ProyectoFrontBundle:Default:contacto.html.twig', $array);
	}
*/
}
