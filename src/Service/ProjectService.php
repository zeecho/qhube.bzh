<?php

namespace App\Service;

use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectService
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function getProjects()
    {
        return [
            'wca' => [
                'name' => 'World Cube Association',
                'format' => 'yml',
                'source' => "https://github.com/thewca/worldcubeassociation.org",
                'website' => "https://www.worldcubeassociation.org",
                'quality' => [
                    'br' => [
                        'reviewed' => false
                    ],
                    'galo' => [
                        'reviewed' => false
                    ],
                ]
            ],
            'afs' => [
                'name' => 'Association Française de Speedcubing',
                'format' => 'yml',
                'source' => "https://github.com/speedcubingfrance/speedcubingfrance.org",
                'website' => "http://www.speedcubingfrance.org/",
                'quality' => [
                    'br' => [
                        'reviewed' => false
                    ],
                    'galo' => [
                        'reviewed' => false
                    ],
                    'frp' => [
                        'reviewed' => true
                    ]
                ]
            ],
            'comp_pages' => [
                'name' => $this->translator->trans('translation.projects.titles.comp_tabs'),
                'format' => 'tsv',
                'quality' => [
                    'br' => [
                        'reviewed' => false
                    ]
                ]
            ],
            'groupifier' => [
                'name' => 'Groupifier',
                'format' => 'yml',
                'source' => "https://github.com/jonatanklosko/groupifier",
                'website' => "https://groupifier.jonatanklosko.com/",
            ],
            'tnoodle' => [
                'name' => 'TNoodle (FMC)',
                'format' => 'yml',
                'source' => "https://github.com/thewca/tnoodle",
                'website' => "https://www.worldcubeassociation.org/regulations/scrambles/",
                'quality' => [
                    'br' => [
                        'reviewed' => true
                    ]
                ]
            ],
            'faq_afs' => [
                'name' => 'FAQ AFS',
                'format' => 'yml',
                'source' => "",
                'website' => "",
                'quality' => [
                    'br' => [
                        'reviewed' => false
                    ],
                    'galo' => [
                        'reviewed' => false
                    ],
                ]
            ],
        ];
    }
}