<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use XMLWriter;
use Illuminate\Support\Facades\File;

trait UploadsFile
{
    public function getExportDirectory($userDirectory): string
    {
        $dir = storage_path("app/public/{$userDirectory}");
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0777, true);
        }
        return $dir;
    }

    public function saveFIle(UploadedFile $file, string $dir = ''): string
    {
        $name = $file->getClientOriginalName();
        $fileName = pathinfo($name, PATHINFO_FILENAME);
        $fileName = str_replace(" ", "-", $fileName.'-'.time());
        $extension = $file->getClientOriginalExtension();
        $file->storeAs("/public/{$dir}", "{$fileName}.{$extension}");
        return "/storage/{$dir}/{$fileName}.{$extension}";
    }

    public function prepareXML($path, $root): XMLWriter
    {
        $xml = new XMLWriter();
        $xml->openURI($path);
        $xml->startDocument('1.0');
        $xml->startElement($root);
        $xml->setIndent(4);
        return $xml;
    }

    public function closeXML(XMLWriter $xml): void
    {
        $xml->endElement();
        $xml->endDocument();
        $xml->flush();
    }
}
