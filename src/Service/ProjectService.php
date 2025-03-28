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
                'format' => 'yml'
            ],
            'tnoodle' => [
                'name' => 'TNoodle (FMC)',
                'format' => 'yml',
                'quality' => [
                    'br' => [
                        'reviewed' => true
                    ]
                ]
            ],
            'faq_afs' => [
                'name' => 'FAQ AFS',
                'format' => 'yml',
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