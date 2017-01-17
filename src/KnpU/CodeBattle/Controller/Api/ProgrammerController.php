<?php

namespace KnpU\CodeBattle\Controller\Api;

use KnpU\CodeBattle\Controller\BaseController;
use KnpU\CodeBattle\Model\Programmer;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProgrammerController extends BaseController
{
    protected function addRoutes(ControllerCollection $controllers)
    {
        $controllers->post('/api/programmers', [$this, 'newAction']);

        $controllers->get('/api/programmers', [$this, 'listAction']);

        $controllers->get('/api/programmers/{nickname}', [$this, 'showAction'])
            ->bind('api_programmers_show');
    }

    public function newAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $programmer = new Programmer();
        $programmer->nickname = $data['nickname'];
        $programmer->avatarNumber = $data['avatarNumber'];
        $programmer->tagLine = $data['tagLine'];
        $programmer->userId = $this->findUserByUsername('jon')->id;

        $this->save($programmer);

        $url = $this->generateUrl('api_programmers_show', [
            'nickname' => $programmer->nickname,
        ]);

        $data = $this->serializeProgrammer($programmer);

        $response = new JsonResponse($data, 201);
        $response->headers->set('Location', $url);
        return $response;
    }

    public function showAction($nickname)
    {
        /**
         * @var Programmer $programmer
         */
        $programmer = $this->getProgrammerRepository()
            ->findOneByNickname($nickname);

        if (!$programmer) {
            $this->throw404('Crap! This programmer has deserted! We\'ll send a search party');
        }

        $data = $this->serializeProgrammer($programmer);

        $response = new JsonResponse($data, 200);
        return $response;
    }

    public function listAction()
    {
        /**
         * @var Programmer[] $programmers
         */
        $programmers = $this->getProgrammerRepository()
            ->findAll();

        $data = ['programmers' => []];

        foreach ($programmers as $programmer) {
            $data['programmers'][] = $this->serializeProgrammer($programmer);
        }

        $response = new JsonResponse($data, 200);
        return $response;
    }

    private function serializeProgrammer(Programmer $programmer)
    {
        return [
            'nickname' => $programmer->nickname,
            'avatarNumber' => $programmer->avatarNumber,
            'powerLevel' => $programmer->powerLevel,
            'tagLine' => $programmer->tagLine,
        ];
    }



















}