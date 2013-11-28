<?php
namespace Celd\ProfileGui\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Elastica\Document;
use Elastica\Result;
use Celd\ProfileGui\Repository\Profiles as ProfilesRepo;



class FilterController
{

    public function hostAction(Request $req, Application $app, $host)
    {
        $repo = new ProfilesRepo();
        $data = $repo->getAverage();
        $profiles = $repo->getLatestWithFilter('host', $host);

        //return $app->json($data);
        return $app['twig']->render('host.html.twig', array(
                'title' => 'ProfileGUI - Home',
                'histogram' => $data,
                'profiles' => $profiles['hits']['hits']
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
