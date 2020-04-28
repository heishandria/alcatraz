<?php

namespace App\Controller;

use App\Annotations\ApiResource;
use App\Entity\Test;
use App\Handler\FormHandler;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exception\InvalidFormException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;

/**
 * Survey controller
 *
 * @SWG\Tag(name="Test")
 * @ApiResource("Test")
 */
class TestController extends AbstractFOSRestController
{
    /**
     * @var FormHandler $formHandler
     */
    private $formHandler;

    /**
     * TestController constructor.
     *
     * @param FormHandler $formHandler
     */
    public function __construct(FormHandler $formHandler)
    {
        $this->formHandler = $formHandler;
    }

    /**
     * Get test by ID
     *
     * @SWG\Parameter(
     *     name="test",
     *     in="path",
     *     type="integer",
     *     description="Test ID"
     * )
     * @SWG\Response(
     *     response=Response::HTTP_OK,
     *     description="Returns an test",
     *     @ApiDoc\Model(type=Test::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NOT_FOUND,
     *     description="When test not found",
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @Rest\Get("/tests/{id}"), name="test_show", requirements={"id":"\d+"}
     * @Rest\View(serializerGroups={"test.details", "test.ext.details"})
     *
     * @param Test $test
     * @return View
     */
    public function getTestAction(Test $test)
    {
        if (!$test) {
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            $statusCode = Response::HTTP_OK;
        }

        return $this->view($test, $statusCode);
    }

    /**
     * List all tests
     *
     * @Rest\Get("/tests"), name="test_list"
     * @Rest\View(serializerGroups={"test.details", "test.ext.details"})
     *
     * @Rest\QueryParam(name="limit",  map=false,  requirements="\d+", nullable=true,   description="Number of test to return")
     *
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="The field used to limit results"
     * )
     * @SWG\Response(
     *     response=Response::HTTP_OK,
     *     description="Returns a list of tests",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@ApiDoc\Model(type=Test::class))
     *     )
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NOT_FOUND,
     *     description="When test not found",
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return View
     * @throws NotFoundHttpException
     */
    public function getTestsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        /** @var array $criteria */
        $criteria = [];

        $limit = $paramFetcher->get('limit') ?? null;

        /** @var array $tests */
        $tests = $this->formHandler->getAll($criteria, $limit);

        if (!$tests) {
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            $statusCode = Response::HTTP_OK;
        }

        return $this->view($tests, $statusCode);
    }

    /**
     * Post test
     *
     * @Rest\Post("/tests"), name="test_create"
     * @Rest\View(serializerGroups={"test.details", "test.ext.details"})
     *
     * @SWG\Parameter(
     * 		name="test",
     * 		in="body",
     * 		required=true,
     * 		@ApiDoc\Model(type=Test::class)
     *	)
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description="Returns the created test",
     *     @ApiDoc\Model(type=Test::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_BAD_REQUEST,
     *     description="When wrong data is provided"
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @return Response
     */
    public function postTestAction(Request $request)
    {
        try {
            /** @var Test $test */
            $test = $this->formHandler->create(
                $request
            );

            return $this->handleView($this->view($test, Response::HTTP_CREATED));
        } catch (InvalidFormException $exception) {
            $errors = $exception->getErrorMessages($exception->getForm());
            return $this->handleView($this->view($errors, Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Put test
     *
     * @Rest\Put("/tests/{id}"), name="test_update"
     * @Rest\View(serializerGroups={"test.details", "test.ext.details"})
     *
     * @SWG\Parameter(
     *     name="test",
     *     in="path",
     *     type="integer",
     *     description="Test ID"
     * )
     * @SWG\Parameter(
     * 		name="test",
     * 		in="body",
     * 		required=true,
     * 		@ApiDoc\Model(type=Test::class)
     *	)
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description="Returns the updated test",
     *     @ApiDoc\Model(type=Test::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_BAD_REQUEST,
     *     description="When wrong data is provided"
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @param Test $test
     * @return Response
     */
    public function putTestAction(Request $request, Test $test)
    {
        try {
            /** @var Test $test */
            $test = $this->formHandler->update(
                $request,
                $test
            );

            return $this->handleView($this->view($test, Response::HTTP_OK));
        } catch (InvalidFormException $exception) {
            $errors = $exception->getErrorMessages($exception->getForm());
            return $this->handleView($this->view($errors, Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Partially update test
     *
     * @Rest\Patch("/tests/{id}"), name="test_update_field"
     * @Rest\View(serializerGroups={"test.details", "test.ext.details"})
     *
     * @SWG\Parameter(
     *     name="test",
     *     in="path",
     *     type="integer",
     *     description="Test ID"
     * )
     * @SWG\Parameter(
     * 		name="test",
     * 		in="body",
     * 		required=true,
     * 		@ApiDoc\Model(type=Test::class)
     *	)
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description="Returns the updated test",
     *     @ApiDoc\Model(type=Test::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_BAD_REQUEST,
     *     description="When wrong data is provided"
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @param Test $test
     * @return Response
     */
    public function patchTestAction(Request $request, Test $test)
    {
        try {
            /** @var Test $test */
            $test = $this->formHandler->update(
                $request,
                $test
            );

            return $this->handleView($this->view($test, Response::HTTP_OK));
        } catch (InvalidFormException $exception) {
            $errors = $exception->getErrorMessages($exception->getForm());
            return $this->handleView($this->view($errors, Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Delete test
     *
     * @Rest\Delete("/tests/{id}"), name="test_delete"
     *
     * @SWG\Parameter(
     *     name="test",
     *     in="path",
     *     type="integer",
     *     description="Test ID"
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NO_CONTENT,
     *     description="Returns an empty message",
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NOT_FOUND,
     *     description="When test not found",
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Test $test
     * @return Response
     */
    public function deleteTestAction(Test $test)
    {
        /** @var Test $test */
        $this->formHandler->delete(
            $test
        );

        return $this->handleView($this->view("test resource deleted", Response::HTTP_NO_CONTENT));
    }
}
