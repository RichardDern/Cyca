<?php

/**
 * This array provides information about import adapters
 */
return [
    /**
     * Available adapters
     */
    'adapters' => [
        /**
         * Cyca. Imports a file built by Cyca itself, in the form of a json
         * array containing all the data stored for a particular user
         */
        'cyca' => [
            /**
             * Class used as adapter
             */
            'adapter' => \App\ImportAdapters\Cyca::class,
            /**
             * Name of the vue component used in the import form
             */
            'view' => 'import-from-cyca',
            /**
             * Adapter's displayed name
             */
            'name' => "Cyca"
        ]
    ]
];
