<?php
namespace AppBundle\Controller;

//use Guzzle\Http\Message\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use AppBundle\Entity;
use Symfony\Component\Validator\Constraints\Time;
use AppBundle\Form\Type\StorageType;
use AppBundle\Form\Type\EventType;

//use Liuggio\ExcelBundle;

//use Symfony\Component\Validator\Constraints\Page;

class AdminController extends Controller
{
	protected $pageSize = 30;
	/**
	 * @Route("/admin/", name="admin_index")
	 */
	public function indexAction()
	{
		return $this->render('AppBundle:admin:index.html.twig');
	}
	/**
	 * @Route("/admin/account/", name="admin_account")
	 */
	public function accountAction()
	{
		
	}
	/**
	 * @Route("/admin/visit", name="admin_visit")
	 */
	public function visitAction(Request $request)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:Visit');
		$queryBuilder = $repository->createQueryBuilder('a');
		
		$query = $queryBuilder->getQuery();
		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$query,
			$request->query->get('page', 1),/*page number*/
			$this->pageSize
			);
		return $this->render('AppBundle:admin:visit.html.twig', array('pagination'=>$pagination));
	}
	/**
	 * @Route("/admin/storage", name="admin_storage")
	 */
	public function storageAction(Request $request)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:Storage');
		$queryBuilder = $repository->createQueryBuilder('a');
		
		$query = $queryBuilder->getQuery();
		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$query,
			$request->query->get('page', 1),/*page number*/
			$this->pageSize
			);
		return $this->render('AppBundle:admin:storage.html.twig', array('pagination'=>$pagination));
	}
	/**
	 * @Route("/admin/storage/add", name="admin_storage_add")
	 */
	public function storageAddAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$storage = new Entity\Storage();
		$form = $this->createForm(new StorageType(), $storage);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$data = $form->getData();
			$fileDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads';
			
			$mapUrl = null;
			$file = $data->getMapUrl();
			if( null != $file ){
				$mapUrl = md5(uniqid()).'.'.$file->guessExtension();
				$file->move($fileDir, $mapUrl);
			}
			$storage->setMapUrl($mapUrl);

			$imgUrl = null;
			$file = $data->getImgUrl();
			if( null != $file ){
				$imgUrl = md5(uniqid()).'.'.$file->guessExtension();
				$file->move($fileDir, $imgUrl);
			}
			$storage->setImgUrl($imgUrl);

			$storage->setCreateTime(new \DateTime("now"));
			$storage->setCreateIp($this->container->get('request')->getClientIp());

			$em->persist($storage);
			$em->flush();
			return $this->redirectToRoute('admin_storage');
		}
		return $this->render('AppBundle:admin:storageForm.html.twig', array(
			'form' => $form->createView(),
			));
	}
	/**
	 * @Route("/admin/storage/edit/{id}", name="admin_storage_edit")
	 */
	public function storageEditAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$storage = $em->getRepository('AppBundle:Storage')->find($id);
		$form = $this->createForm(new StorageType(), $storage);
		$form->handleRequest($request);
		$mapUrl = $storage->getMapUrl();
		$imgUrl = $storage->getImgUrl();
		if ($form->isValid()) {
			$data = $form->getData();
			$fileDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads';
			
			$file = $data->getMapUrl();
			if( null != $file ){
				$mapUrl = md5(uniqid()).'.'.$file->guessExtension();
				$file->move($fileDir, $mapUrl);
			}
			$storage->setMapUrl($mapUrl);


			$file = $data->getImgUrl();
			if( null != $file ){
				$imgUrl = md5(uniqid()).'.'.$file->guessExtension();
				$file->move($fileDir, $imgUrl);
			}
			$storage->setImgUrl($imgUrl);
			//$storage->setCreateTime(new \DateTime("now"));
			//$storage->setCreateIp($this->container->get('request')->getClientIp());

			$em->persist($storage);
			$em->flush();
			return $this->redirectToRoute('admin_storage');
		}
		return $this->render('AppBundle:admin:storageForm.html.twig', array(
			'form' => $form->createView(),
			));
	}
	/**
	 * @Route("/admin/storage/delete/{id}", name="admin_storage_delete")
	 */
	public function storageDeleteAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$storage = $em->getRepository('AppBundle:Storage')->find($id);
		$em->remove($storage);
		$em->flush();
		return new Response(json_encode(array('ret'=>0, 'msg'=>'')));
	}
	/**
	 * @Route("/admin/event", name="admin_event")
	 */
	public function eventAction(Request $request)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:Event');
		$queryBuilder = $repository->createQueryBuilder('a');
		
		$query = $queryBuilder->getQuery();
		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$query,
			$request->query->get('page', 1),/*page number*/
			$this->pageSize
			);
		return $this->render('AppBundle:admin:event.html.twig', array('pagination'=>$pagination));
	}
	/**
	 * @Route("/admin/event/add", name="admin_event_add")
	 */
	public function eventAddAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$event = new Entity\Event();
		$form = $this->createForm(new EventType(), $event);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$data = $form->getData();
			$fileDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads';
			
			$imgUrl = null;
			$file = $data->getImgUrl();
			if( null != $file ){
				$imgUrl = md5(uniqid()).'.'.$file->guessExtension();
				$file->move($fileDir, $imgUrl);
			}
			$event->setImgUrl($imgUrl);
			$event->setCreateTime(new \DateTime("now"));
			$event->setCreateIp($this->container->get('request')->getClientIp());

			$em->persist($event);
			$em->flush();
			return $this->redirectToRoute('admin_event');
		}
		return $this->render('AppBundle:admin:eventForm.html.twig', array(
			'form' => $form->createView(),
			));
	}
	/**
	 * @Route("/admin/event/edit/{id}", name="admin_event_edit")
	 */
	public function eventEditAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$event = $em->getRepository('AppBundle:Event')->find($id);
		$form = $this->createForm(new EventType(), $event);
		$form->handleRequest($request);
		$imgUrl = $event->getImgUrl();
		if ($form->isValid()) {
			$data = $form->getData();
			$fileDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads';
			
			$file = $data->getMapUrl();
			if( null != $file ){
				$imgUrl = md5(uniqid()).'.'.$file->guessExtension();
				$file->move($fileDir, $imgUrl);
			}
			$event->setImgUrl($imgUrl);
			//$storage->setCreateTime(new \DateTime("now"));
			//$storage->setCreateIp($this->container->get('request')->getClientIp());

			$em->persist($event);
			$em->flush();
			return $this->redirectToRoute('admin_event');
		}
		return $this->render('AppBundle:admin:eventForm.html.twig', array(
			'form' => $form->createView(),
			));
	}

	/**
	 * @Route("/admin/event/delete/{id}", name="admin_event_delete")
	 */
	public function eventDeleteAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$event = $em->getRepository('AppBundle:Event')->find($id);
		$em->remove($event);
		$em->flush();
		return new Response(json_encode(array('ret'=>0, 'msg'=>'')));
	}
	/**
	 * @Route("/admin/export/", name="admin_export")
	 */
	public function exportAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('AppBundle:LotteryLog');
		$queryBuilder = $repository->createQueryBuilder('a')
		->leftjoin('a.lottery','b')
		->leftjoin('b.prize','c');
		$queryBuilder->where('c.id != 5');
		$queryBuilder->orderBy('a.createTime', 'DESC');
		$logs = $queryBuilder->getQuery()->getResult();
		//$output = '';
		$arr = array(
			'id,奖项,姓名,手机,地址,抽奖时间,抽奖IP'
			);
		foreach($logs as $v){
			$member = $em->getRepository('AppBundle:Member')->findOneBySessionId($v->getSessionId());
			$_string = $v->getId().','.$v->getLottery()->getPrize()->getTitle().',';
			if( isset($member))
				$_string .= $member->getName().','.$member->getTel().','.$member->getAddress().',';
			else
				$_string .= '-,-,-,';
			$_string .= $v->getCreateTime()->format('Y-m-d H:i:s').','.$v->getCreateIp().',';
			$arr[] = $_string;
		}
		$output = implode("\n", $arr);

		//$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
		/*
		$phpExcelObject = new \PHPExcel();
		$phpExcelObject->getProperties()->setCreator("liuggio")
			->setLastModifiedBy("Giulio De Donato")
			->setTitle("Office 2005 XLSX Test Document")
			->setSubject("Office 2005 XLSX Test Document")
			->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
			->setKeywords("office 2005 openxml php")
			->setCategory("Test result file");
		$phpExcelObject->setActiveSheetIndex(0);
		foreach($logs as $v){
			$phpExcelObject->setCellValue('A1', $v->getId());
		}
		$phpExcelObject->getActiveSheet()->setTitle('Simple');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$phpExcelObject->setActiveSheetIndex(0);

		// create the writer
		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
		// create the response
		$response = $this->get('phpexcel')->createStreamedResponse($writer);
		// adding headers
		$dispositionHeader = $response->headers->makeDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT,
			'stream-file.xls'
		);
		$response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');
		$response->headers->set('Content-Disposition', $dispositionHeader);
		*/

		$response = new Response($output);
		$response->headers->set('Content-Disposition', ':attachment; filename=data.csv');
		$response->headers->set('Content-Type', 'text/csv; charset=utf-8');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');
		return $response;
	}

}
