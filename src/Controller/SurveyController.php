<?php


namespace App\Controller;

use App\Annotations\ApiResource;
use App\Entity\Survey;
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
 * @SWG\Tag(name="Survey")
 * @ApiResource("Survey")
 */
class SurveyController extends AbstractFOSRestController
{
    /**
     * @var FormHandler $formHandler
     */
    private $formHandler;

    /**
     * SurveyController constructor.
     *
     * @param FormHandler $formHandler
     */
    public function __construct(FormHandler $formHandler)
    {
        $this->formHandler = $formHandler;
    }

    /**
     * Get survey by ID
     *
     * @Rest\Get("/surveys/{id}"), name="survey_show", requirements={"id":"\d+"}
     * @Rest\View(serializerGroups={"survey.details", "survey.ext.details", "default"})
     *
     * @SWG\Parameter(
     *     name="survey",
     *     in="path",
     *     type="integer",
     *     description="Survey ID"
     * )
     * @SWG\Response(
     *     response=Response::HTTP_OK,
     *     description="Returns an survey",
     *     @ApiDoc\Model(type=Survey::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NOT_FOUND,
     *     description="When survey not found",
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Survey $survey
     * @return View
     */
    public function getSurveyAction(Survey $survey)
    {
        return $this->view($survey, !$survey ? Response::HTTP_NOT_FOUND : Response::HTTP_OK);
    }

    /**
     * List all surveys
     *
     * @Rest\Get("/surveys"), name="survey_list"
     *
     * @Rest\QueryParam(name="active", map=false, requirements="\d+", nullable=true, description="Active surveys")
     * @Rest\QueryParam(name="limit", map=false, requirements="\d+", nullable=true, description="Number of survey to return")
     * @Rest\View(serializerGroups={"survey.details", "survey.ext.details", "default"})
     *
     * @param Request $request
     * @param ParamFetcherInterface $paramFetcher
     *
     * @SWG\Parameter(
     *     name="active",
     *     in="query",
     *     type="integer",
     *     enum={0,1},
     *     description="Survey active status"
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="The field used to limit results"
     * )
     * @SWG\Response(
     *     response=Response::HTTP_OK,
     *     description="Returns a list of surveys",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@ApiDoc\Model(type=Survey::class))
     *     )
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NOT_FOUND,
     *     description="When survey not found",
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @return View
     * @throws NotFoundHttpException
     */
    public function getSurveysAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        /** @var Array $criteria */
        $criteria = [];

        if (null !== $paramFetcher->get('active')) {
            $criteria['active'] = $paramFetcher->get('active');
        }

        $limit = $paramFetcher->get('limit') ?? null;
        /** @var Array $surveys */
        $surveys = $this->formHandler->getAll($criteria, $limit);

        return $this->view($surveys, !$surveys ? Response::HTTP_NOT_FOUND : Response::HTTP_OK);
    }

    /**
     * Post survey
     *
     * @Rest\Post("/surveys"), name="survey_create"
     * @Rest\View(serializerGroups={"survey.details", "survey.ext.details", "default"})
     *
     * @SWG\Parameter(
     * 		name="survey",
     * 		in="body",
     * 		required=true,
     * 		@ApiDoc\Model(type=Survey::class)
     *	)
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description="Returns the created survey",
     *     @ApiDoc\Model(type=Survey::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_BAD_REQUEST,
     *     description="When wrong data is provided"
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @return View|Response
     */
    public function postSurveyAction(Request $request)
    {
        try {
            /** @var Survey $survey */
            $survey = $this->formHandler->create(
                $request
            );

            return $this->view($survey,Response::HTTP_CREATED);
        } catch (InvalidFormException $exception) {
            $errors = $exception->getErrorMessages($exception->getForm());
            return $this->handleView($this->view($errors, Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Put survey
     *
     * @Rest\Put("/surveys/{id}"), name="survey_update"
     * @Rest\View(serializerGroups={"survey.details", "survey.ext.details", "default"})
     *
     * @SWG\Parameter(
     *     name="survey",
     *     in="path",
     *     type="integer",
     *     description="Survey ID"
     * )
     * @SWG\Parameter(
     * 		name="survey",
     * 		in="body",
     * 		required=true,
     * 		@ApiDoc\Model(type=Survey::class)
     *	)
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description="Returns the updated survey",
     *     @ApiDoc\Model(type=Survey::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_BAD_REQUEST,
     *     description="When wrong data is provided"
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @param Survey $survey
     * @return View|Response
     */
    public function putSurveyAction(Request $request, Survey $survey)
    {
        try {
            /** @var Survey $survey */
            $survey = $this->formHandler->update(
                $request,
                $survey
            );

            return $this->view($survey,Response::HTTP_OK);
        } catch (InvalidFormException $exception) {
            $errors = $exception->getErrorMessages($exception->getForm());
            return $this->handleView($this->view($errors, Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Partially update survey
     *
     * @Rest\Patch("/surveys/{id}"), name="survey_update_field"
     * @Rest\View(serializerGroups={"survey.details", "survey.ext.details", "default"})
     *
     * @SWG\Parameter(
     *     name="survey",
     *     in="path",
     *     type="integer",
     *     description="Survey ID"
     * )
     * @SWG\Parameter(
     * 		name="survey",
     * 		in="body",
     * 		required=true,
     * 		@ApiDoc\Model(type=Survey::class)
     *	)
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description="Returns the updated survey",
     *     @ApiDoc\Model(type=Survey::class)
     * )
     * @SWG\Response(
     *     response=Response::HTTP_BAD_REQUEST,
     *     description="When wrong data is provided"
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Request $request
     * @param Survey $survey
     * @return View|Response
     */
    public function patchSurveyAction(Request $request, Survey $survey)
    {
        try {
            /** @var Survey $survey Update partially */
            $survey = $this->formHandler->update(
                $request,
                $survey
            );

            return $this->view($survey,Response::HTTP_OK);
        } catch (InvalidFormException $exception) {
            $errors = $exception->getErrorMessages($exception->getForm());
            return $this->handleView($this->view($errors, Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Delete survey
     *
     * @Rest\Delete("/surveys/{id}"), name="survey_delete"
     *
     * * @SWG\Parameter(
     *     name="survey",
     *     in="path",
     *     type="integer",
     *     description="Survey ID"
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NO_CONTENT,
     *     description="Returns an empty message",
     * )
     * @SWG\Response(
     *     response=Response::HTTP_NOT_FOUND,
     *     description="When survey not found",
     * )
     *
     * @ApiDoc\Security(name="Bearer")
     *
     * @param Survey $survey
     * @return Response
     */
    public function deleteSurveyAction(Survey $survey)
    {
        /** @var Survey $survey */
        $this->formHandler->delete(
            $survey
        );

        return $this->handleView($this->view("survey resource deleted", Response::HTTP_NO_CONTENT));
    }
}