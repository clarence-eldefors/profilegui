<?php
namespace Celd\ProfileGui\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Elastica\Document;
use Elastica\Result;
use Celd\ProfileGui\Repository\Profiles as ProfilesRepo;



class DefaultController
{

    public function testAction(Request $req, Application $app)
    {
        return $app->json(array());
    }

    public function indexAction(Request $req, Application $app)
    {
        $repo = new ProfilesRepo();
        $repo->refreshIndex();
        $data = $repo->getLatest();
        return $app['twig']->render('index.html.twig', array(
                'title' => 'ProfileGUI - Home',
                'profiles' => $data['hits']['hits']
         ));
    }

    public function backup () {
        $repo = new Profiles();

        // Refresh index
        $repo->refreshIndex();

        $resultSet = $repo->search('profilegui.tv.nu');
        $docs = array();
        //$that = new Result();
        foreach($resultSet as $hit) {

            $docs[] = $hit->getSource();
        }

        $avg = $repo->getAverage();

        return $app->json($avg);
    }

}
