<?php
use In2code\Lux\Domain\Model\Linkclick;

return [
    'ctrl' => [
        'title' => 'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:' . Linkclick::TABLE_NAME,
        'label' => 'tag',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY crdate DESC',
        'delete' => 'deleted',
        'iconfile' => 'EXT:lux/Resources/Public/Icons/' . Linkclick::TABLE_NAME . '.svg',
        'rootLevel' => -1
    ],
    'interface' => [
        'showRecordFieldList' => 'crdate,title,link,category',
    ],
    'types' => [
        '1' => ['showitem' => 'crdate,title,link,category'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'default' => 0,
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0]
                ]
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0]
                ],
                'foreign_table' => Linkclick::TABLE_NAME,
                'foreign_table_where' => 'AND ' . Linkclick::TABLE_NAME . '.pid=###CURRENT_PID### AND ' .
                    Linkclick::TABLE_NAME . '.sys_language_uid IN (-1,0)',
                'default' => 0
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'crdate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:' . Linkclick::TABLE_NAME . '.crdate',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'datetime',
                'readOnly' => true
            ]
        ],
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:' . Linkclick::TABLE_NAME . '.title',
            'config' => [
                'type' => 'input',
                'readOnly' => true
            ]
        ],
        'link' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:' . Linkclick::TABLE_NAME . '.link',
            'config' => [
                'type' => 'input',
                'readOnly' => true
            ]
        ],
        'category' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:' . Linkclick::TABLE_NAME . '.category',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'sys_category',
                'foreign_table_where' => 'lux_category=1 ORDER BY title ASC',
                'default' => 0,
                'readOnly' => true
            ],
        ],
    ]
];
