<?php

namespace App\Handler;

use App\Exception\InvalidFormException;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class FormHandler
 *
 * @package App\Handler
 */
Class FormHandler
{
    const ENTITY_PATH = 'App\Entity\\';
    const FORM_PATH = 'App\Form\\';

    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    /**
     * @var FormFactoryInterface $formFactory
     */
    protected $formFactory;

    /**
     * @var RequestStack RequestStack
     */
    protected $requestStack;

    /**
     * @var string $resource
     */
    private $resource;

    /**
     * FormHandler constructor.
     * @param EntityManagerInterface $em
     * @param FormFactoryInterface $formFactory
     * @param RequestStack $requestStack
     */
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
    }

    /**
     * Get all objects
     *
     * @param array $criteria
     * @param mixed $limit
     *
     * @return Array|null
     */
    public function getAll(?Array $criteria, $limit): ?Array
    {
        /** @var String $resource */
        $resource = $this->requestStack->getCurrentRequest()->attributes->get('_resource');
        return $this->em->getRepository(self::ENTITY_PATH.$resource)->getAll($criteria, $limit);
    }

    /**
     * Object creation
     *
     * @param Request $request
     *
     * @return Object
     */
    public function create(Request $request)
    {
        /** Method ProcessForm */
        return $this->processForm(null, $request);
    }

    /**
     * Update question (PUT, PATCH)
     *
     * @param Request $request
     * @param mixed $object
     *
     * @return Object
     */
    public function update(Request $request, $object)
    {
        /** Method ProcessForm */
        return $this->processForm($object, $request);
    }

    /**
     * Delete object
     *
     * @param $object
     * @return bool
     */
    public function delete($object)
    {
        $this->em->remove($object);
        $this->em->flush();

        return true;
    }

    /**
     * Processes the form.
     *
     * @param mixed $objectTemp
     * @param Request $request
     *
     * @return Object
     *
     * @throws InvalidFormException
     *
     */
    private function processForm($objectTemp, Request $request)
    {
        $this->resource = $request->attributes->get('_resource');

        if (null === $objectTemp) {
            $tempName = self::ENTITY_PATH.$this->resource;
            $objectTemp = new $tempName();
        }

        /** @var FormInterface $form */
        $form = $this->formFactory->create(
            self::FORM_PATH.$this->resource.'Type',
            $objectTemp, array('method' => $request->getMethod())
        );

        $object = json_decode($request->getContent(), true);

        /** Submit manually object */
        $form->submit($object, 'PATCH' !== $request->getMethod());

        if ($form->isValid()) {
            $object = $form->getData();

            if ($request->isMethod('POST')) {
                $this->em->persist($object);
            }

            /** Flushing in DB */
            $this->em->flush();
            return $object;
        }

        throw new InvalidFormException(500, 'Invalid data', $form);
    }
}

