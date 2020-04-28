<?php

namespace App\Controller;

use App\Annotations\ApiResource;
use App\Entity\Response;
use App\Handler\FormHandler;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use App\Exception\InvalidFormException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;

/**
 * Response controller
 *
 * @SWG\Tag(name="Response")
 * @ApiResource("Response")
 */
class ResponseController extends AbstractFOSRestController
{
    /**
     * @var FormHandler $formHandler
     */
    private $formHandler;

    /**
     * ResponseController constructor.
     *
     * @param FormHandler $formHandler
     */
    public function __construct(FormHandler $formHandler)
    {
        $this->formHandler = $formHandler;
    }

    /**
     * Get response by ID
     *
     * @Rest\Get("/responses/{id}"), name="response_show", requirements={"id":"\d+"}
     * @Rest\View(serializerGroups={"response.details", "response.ext.details", "default"})
     *
     * @SWG\Parameter(
     *     name="response",
     *     in="path",
     *     type="integer",
     *     description="Response ID"
     * )
     * @SWG\Response(
     *     response=HttpResponse::HTTP_OK,
     *     description="Returns an response",
     *     @ApiDoc\Model(type=Response::class)
     * )
     * @SWG\Response(
     *     response=HttpResponse::HTTP_NOT_FOUND,
     *     description="When response not found",
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Response $response
     * @return View
     */
    public function getResponseAction(Response $response)
    {
        if (!$response) {
            $statusCode = HttpResponse::HTTP_NOT_FOUND;
        } else {
            $statusCode = HttpResponse::HTTP_OK;
        }

        return $this->view($response, $statusCode);
    }

    /**
     * List all responses
     *
     * @Rest\Get("/responses"), name="response_list"
     * @Rest\View(serializerGroups={"response.details", "response.ext.details", "default"})
     *
     * @Rest\QueryParam(name="active",  map=false,  requirements="\d+", nullable=true,  description="Active responses")
     * @Rest\QueryParam(name="limit",  map=false,  requirements="\d+", nullable=true,   description="Number of responses to return")
     *
     * @SWG\Parameter(
     *     name="active",
     *     in="query",
     *     type="integer",
     *     enum={0,1},
     *     description="Response active status"
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="The field used to limit results"
     * )
     * @SWG\Response(
     *     response=HttpResponse::HTTP_OK,
     *     description="Returns a list of responses",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@ApiDoc\Model(type=Response::class))
     *     )
     * )
     * @SWG\Response(
     *     response=HttpResponse::HTTP_NOT_FOUND,
     *     description="When response not found",
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
    public function getResponsesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        /** @var array $criteria */
        $criteria = [];

        if (null !== $paramFetcher->get('active')) {
            $criteria['active'] = $paramFetcher->get('active');
        }

        $limit = $paramFetcher->get('limit') ?? null;

        /** @var array $responses */
        $responses = $this->formHandler->getAll($criteria, $limit);

        if (!$responses) {
            $statusCode = HttpResponse::HTTP_NOT_FOUND;
        } else {
            $statusCode = HttpResponse::HTTP_OK;
        }

        return $this->view($responses, $statusCode);
    }

    /**
     * Post response
     *
     * @SWG\Parameter(
     * 		name="response",
     * 		in="body",
     * 		required=true,
     * 		@ApiDoc\Model(type=Response::class)
     *	)
     * @SWG\Response(
     *     response=HttpResponse::HTTP_CREATED,
     *     description="Returns the created response",
     *     @ApiDoc\Model(type=Response::class)
     * )
     * @SWG\Response(
     *     response=HttpResponse::HTTP_BAD_REQUEST,
     *     description="When wrong data is provided"
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @Rest\Post("/responses"), name="response_create"
     * @Rest\View(serializerGroups={"response.details", "response.ext.details", "default"})
     *
     * @param Request $request
     * @return HttpResponse|View
     */
    public function postResponseAction(Request $request)
    {
        try {
            /** @var Response $response */
            $response = $this->formHandler->create(
                $request
            );

            return $this->view($response, HttpResponse::HTTP_CREATED);
        } catch (InvalidFormException $exception) {
            $errors = $exception->getErrorMessages($exception->getForm());
            return $this->handleView($this->view($errors, HttpResponse::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Put response
     *
     * @Rest\Put("/responses/{id}"), name="response_update"
     * @Rest\View(serializerGroups={"response.details", "response.ext.details", "default"})
     *
     * @SWG\Parameter(
     *     name="response",
     *     in="path",
     *     type="integer",
     *     description="Response ID"
     * )
     * @SWG\Parameter(
     * 		name="response",
     * 		in="body",
     * 		required=true,
     * 		@ApiDoc\Model(type=Response::class)
     *	)
     * @SWG\Response(
     *     response=HttpResponse::HTTP_CREATED,
     *     description="Returns the updated response",
     *     @ApiDoc\Model(type=Response::class)
     * )
     * @SWG\Response(
     *     response=HttpResponse::HTTP_BAD_REQUEST,
     *     description="When wrong data is provided"
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @param Response $response
     * @return HttpResponse|View
     */
    public function putResponseAction(Request $request, Response $response)
    {
        try {
            /** @var Response $response */
            $response = $this->formHandler->update(
                $request,
                $response
            );

            return $this->view($response, HttpResponse::HTTP_OK);
        } catch (InvalidFormException $exception) {
            $errors = $exception->getErrorMessages($exception->getForm());
            return $this->handleView($this->view($errors, HttpResponse::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Partially update response
     *
     * @Rest\Patch("/responses/{id}"), name="response_update_field"
     * @Rest\View(serializerGroups={"response.details", "response.ext.details", "default"})
     *
     * @SWG\Parameter(
     *     name="response",
     *     in="path",
     *     type="integer",
     *     description="Response ID"
     * )
     * @SWG\Parameter(
     * 		name="response",
     * 		in="body",
     * 		required=true,
     * 		@ApiDoc\Model(type=Response::class)
     *	)
     * @SWG\Response(
     *     response=HttpResponse::HTTP_CREATED,
     *     description="Returns the updated response",
     *     @ApiDoc\Model(type=Response::class)
     * )
     * @SWG\Response(
     *     response=HttpResponse::HTTP_BAD_REQUEST,
     *     description="When wrong data is provided"
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @param Response $response
     * @return HttpResponse|View
     */
    public function patchResponseAction(Request $request, Response $response)
    {
        try {
            /** @var Response $response */
            $response = $this->formHandler->update(
                $request,
                $response
            );

            return $this->view($response, HttpResponse::HTTP_OK);
        } catch (InvalidFormException $exception) {
            $errors = $exception->getErrorMessages($exception->getForm());
            return $this->handleView($this->view($errors, HttpResponse::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Delete response
     *
     * @Rest\Delete("/responses/{id}"), name="response_delete"
     *
     * @SWG\Parameter(
     *     name="response",
     *     in="path",
     *     type="integer",
     *     description="Response ID"
     * )
     * @SWG\Response(
     *     response=HttpResponse::HTTP_NO_CONTENT,
     *     description="Returns an empty message",
     * )
     * @SWG\Response(
     *     response=HttpResponse::HTTP_NOT_FOUND,
     *     description="When response not found",
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Response $response
     * @return HttpResponse
     */
    public function deleteResponseAction(Response $response)
    {
        /** @var Response $response */
        $this->formHandler->delete(
            $response
        );

        return $this->handleView($this->view("Response resource deleted", HttpResponse::HTTP_NO_CONTENT));
    }
}
