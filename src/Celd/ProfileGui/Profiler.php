<?php
namespace Celd\ProfileGui;
use Elastica\Document;
use Elastica\Result;
use Celd\ProfileGui\Repository\Profiles;


class Profiler
{
    public static function profile() {
        ignore_user_abort(true);
        flush();
        $repo = new Profiles();
        $xhprofData = xhprof_disable();

        $docData = array();
        $docData['host'] = $_SERVER['HTTP_HOST'];
        $docData['uri'] = $_SERVER['REQUEST_URI'];
        $docData['tstamp'] = (time()-rand(0, 3600*72))*1000;
        $docData['wt'] = $xhprofData['main()']['wt'];
        $docData['cpu'] = $xhprofData['main()']['cpu'];
        $docData['mu'] = $xhprofData['main()']['mu'];
        $docData['pmu'] = $xhprofData['main()']['pmu'];
        $docData['profile'] = $xhprofData;


        $doc1 = new Document(uniqid(),
            $docData
        );

        $repo->addDocument($doc1);
    }
}