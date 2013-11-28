<?php
namespace Celd\ProfileGui\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Celd\ProfileGui\Repository\Profiles as ProfilesRepo;



class ProfileController
{

    public function indexAction(Request $req, Application $app, $id)
    {
        $repo = new ProfilesRepo();
        $data = $repo->getById($id);

        return $app['twig']->render('profile.html.twig', array(
                'title' => 'ProfileGUI - Home',
                'results' => $data['hits']['hits'][0]['_source']
         ));
    }

}
