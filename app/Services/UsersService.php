<?php

namespace App\Services;

use Illuminate\Http\Request;

class UsersService
{
    public function readFile($file)
    {
        $file_path = storage_path($file . '.json');
        if (! file_exists($file_path)) {
            return;
        }

        $handle = fopen($file_path, 'r');
        $contents = fread($handle, filesize($file_path));
        fclose($handle);
        return $contents;
    }

    public function providerMapping(string $provider): string
    {
        $mapping = [
            'x' => 'DataProviderX',
            'y' => 'DataProviderY',
            'z' => 'DataProviderZ',
            //..,
            //..,
            //etc
        ];
        return array_flip($mapping)[$provider];
    }
}
