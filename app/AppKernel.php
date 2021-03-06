<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // priority project bundles
            new Sygefor\Bundle\ElasticaBundle\SygeforElasticaBundle(),

            // Core
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new MBence\OpenTBSBundle\OpenTBSBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new KULeuven\ShibbolethBundle\ShibbolethBundle(),

            // project bundles
            new Sygefor\Bundle\CoreBundle\SygeforCoreBundle(),
            new Sygefor\Bundle\ApiBundle\SygeforApiBundle(),
            new Sygefor\Bundle\UserBundle\SygeforUserBundle(),
            new Sygefor\Bundle\TaxonomyBundle\SygeforTaxonomyBundle(),
            new Sygefor\Bundle\TrainingBundle\SygeforTrainingBundle(),
            new Sygefor\Bundle\ListBundle\SygeforListBundle(),
            new Sygefor\Bundle\TraineeBundle\SygeforTraineeBundle(),
            new Sygefor\Bundle\TrainerBundle\SygeforTrainerBundle(),
            new Sygefor\Bundle\ElasticaUpdateBundle\SygeforElasticaUpdateBundle(),
            new Sygefor\Bundle\LheoBundle\SygeforLheoBundle(),
            new Sygefor\Bundle\ActivityReportBundle\SygeforActivityReportBundle(),

            // keep this extension at the end to ensure listeners registring order
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
